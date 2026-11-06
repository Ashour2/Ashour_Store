<?php

namespace App\Services;

use App\Models\{AdminNotification, Pharmacy, User, Warehouse, WarehouseStock, WholesaleOrder};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class WholesaleOrderService
{
    public function place(User $user, Warehouse $warehouse, array $data): WholesaleOrder
    {
        abort_unless($user->isPharmacist() && $user->pharmacy_id, 403);
        return DB::transaction(function () use ($user, $warehouse, $data) {
            $warehouse = Warehouse::whereKey($warehouse->id)->lockForUpdate()->firstOrFail();
            abort_unless($warehouse->is_active, 403, 'المستودع غير متاح حالياً.');
            $pharmacy = Pharmacy::orderable()->find($user->pharmacy_id);
            abort_unless($pharmacy, 403, 'يلزم اشتراك صيدلية فعّال لإرسال الطلب.');
            if ($existing = WholesaleOrder::where('request_token', $data['request_token'])->first()) {
                abort_unless($existing->user_id === $user->id && $existing->warehouse_id === $warehouse->id, 403);
                return $existing;
            }

            $quantities = collect($data['quantities'])->filter(fn ($quantity) => (int) $quantity > 0)->sortKeys();
            if ($quantities->isEmpty()) throw ValidationException::withMessages(['quantities' => 'اختر دواء واحداً على الأقل وحدد الكمية.']);
            $stocks = WarehouseStock::with('product')->where('warehouse_id', $warehouse->id)
                ->whereIn('id', $quantities->keys())->orderBy('id')->lockForUpdate()->get()->keyBy('id');
            $lines = [];
            foreach ($quantities as $id => $quantity) {
                $stock = $stocks->get($id);
                if (! $stock || ! $stock->is_available || ! $stock->product?->is_active || $stock->quantity < $quantity) {
                    throw ValidationException::withMessages(['quantities.'.$id => 'الدواء غير متاح أو الكمية المطلوبة تتجاوز مخزون المستودع.']);
                }
                $lines[] = ['product_id' => $stock->product_id, 'product_name' => $stock->product->trans_name,
                    'price' => $stock->price, 'quantity' => (int) $quantity, 'total' => round((float) $stock->price * $quantity, 2)];
            }
            $total = round(array_sum(array_column($lines, 'total')), 2);
            if ($total > 999999999999.99) throw ValidationException::withMessages(['quantities' => 'إجمالي الطلب يتجاوز الحد المسموح. قسّم الكميات إلى طلبات أصغر.']);
            $order = WholesaleOrder::create([
                'number' => 'WHO-'.Str::upper((string) Str::ulid()), 'request_token' => $data['request_token'],
                'warehouse_id' => $warehouse->id, 'pharmacy_id' => $pharmacy->id, 'user_id' => $user->id,
                'pharmacy_name' => $pharmacy->trans_name, 'pharmacy_phone' => $pharmacy->phone,
                'delivery_address' => $data['delivery_address'], 'notes' => $data['notes'] ?? null,
                'total' => $total, 'status' => 'new',
            ]);
            $order->items()->createMany($lines);
            foreach ($quantities as $id => $quantity) $stocks[$id]->decrement('quantity', $quantity);
            $this->notify($order, true, 'طلب توريد جديد', 'طلب جديد من '.$pharmacy->trans_name);
            return $order;
        });
    }

    public function transition(User $user, WholesaleOrder $order, string $status): WholesaleOrder
    {
        return DB::transaction(function () use ($user, $order, $status) {
            Warehouse::whereKey($order->warehouse_id)->lockForUpdate()->firstOrFail();
            $order = WholesaleOrder::with('items')->whereKey($order->id)->lockForUpdate()->firstOrFail();
            $warehouseActor = $user->isWarehouse() && $user->warehouse_id === $order->warehouse_id;
            $pharmacyActor = $user->isPharmacist() && $user->pharmacy_id === $order->pharmacy_id;
            abort_unless($warehouseActor || $pharmacyActor, 403);
            if ($order->status === $status) return $order;
            $next = ['new' => ['accepted', 'cancelled'], 'accepted' => ['preparing', 'cancelled'], 'preparing' => ['completed', 'cancelled']];
            $allowed = $warehouseActor ? ($next[$order->status] ?? []) : ($order->status === 'new' ? ['cancelled'] : []);
            if (!in_array($status, $allowed, true)) throw ValidationException::withMessages(['status' => 'لا يمكن الانتقال إلى هذه الحالة.']);
            if ($status === 'cancelled') {
                foreach ($order->items as $item) {
                    if ($item->product_id) WarehouseStock::where('warehouse_id', $order->warehouse_id)
                        ->where('product_id', $item->product_id)->increment('quantity', $item->quantity);
                }
            }
            $order->update(['status' => $status]);
            $this->notify($order, !$warehouseActor, 'تحديث طلب التوريد', $order->number.' — '.$order->status_label);
            return $order;
        });
    }

    private function notify(WholesaleOrder $order, bool $toWarehouse, string $title, string $body): void
    {
        $users = $toWarehouse
            ? User::where('warehouse_id', $order->warehouse_id)->whereHas('role', fn ($roles) => $roles->where('slug', 'warehouse'))
            : User::where('pharmacy_id', $order->pharmacy_id)->whereHas('role', fn ($roles) => $roles->where('slug', 'pharmacist'));
        foreach ($users->pluck('id') as $userId) AdminNotification::create([
            'user_id' => $userId, 'type' => 'wholesale', 'title' => $title, 'body' => $body,
            'url' => route('admin.wholesale.orders.show', $order),
        ]);
    }
}
