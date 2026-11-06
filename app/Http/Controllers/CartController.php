<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Pharmacy;
use App\Models\Product;
use App\Services\CartService;
use App\Services\AdminNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    public function __construct(protected CartService $cart) {}

    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) return response()->json($this->payload());
        return view('site.cart.index', ['items' => $this->cart->items()]);
    }

    public function add(Request $request, Product $medicine)
    {
        $request->validate(['quantity' => 'nullable|integer|min:1|max:99']);
        abort_unless($medicine->is_active, 404);
        if ($medicine->show_stock && ! $medicine->in_stock) return $this->respond($request, __('site.cart_out_of_stock'), 'danger', 422);
        $this->cart->add($medicine, (int) $request->input('quantity', 1));
        return $this->respond($request, __('site.cart_added'), 'success');
    }

    public function update(Request $request, Product $medicine)
    {
        $request->validate(['quantity' => 'required|integer|min:0|max:99']);
        abort_unless($medicine->is_active, 404);
        $this->cart->update($medicine, (int) $request->quantity);
        return $this->respond($request, __('site.cart_updated'), 'success');
    }

    public function remove(Request $request, int $medicine) { $this->cart->remove($medicine); return $this->respond($request, __('site.cart_removed'), 'info'); }
    public function clear(Request $request) { $this->cart->clear(); return $this->respond($request, __('site.cart_cleared'), 'info'); }

    public function checkout()
    {
        if ($this->cart->isEmpty()) return redirect()->route('medicines.index')->with('cart_msg', __('site.cart_empty'));
        $items = $this->cart->items();
        $pharmacies = $this->availablePharmacies($items)
            ->with(['products' => fn ($products) => $products->whereIn('products.id', $items->pluck('product.id'))])
            ->get();
        $pharmacies->each(function (Pharmacy $pharmacy) use ($items) {
            $held = $pharmacy->products->keyBy('id');
            $pharmacy->quote_visible = $items->every(fn ($item) => (bool) ($held[$item->product->id]?->pivot->show_price ?? $item->product->show_price));
            $pharmacy->quote_total = $items->sum(function ($item) use ($held) {
                $pivot = $held[$item->product->id]?->pivot;
                return ((float) ($pivot?->price ?? $item->product->price)) * $item->quantity;
            });
        });
        return view('site.cart.checkout', ['items' => $items, 'total' => $this->cart->total(), 'pharmacies' => $pharmacies]);
    }

    public function store(Request $request)
    {
        if ($this->cart->isEmpty()) return redirect()->route('medicines.index')->with('cart_msg', __('site.cart_empty'));
        $data = $request->validate([
            'customer_name' => 'required|string|max:100', 'customer_phone' => 'required|string|max:30',
            'customer_address' => 'nullable|string|max:255', 'note' => 'nullable|string|max:1000',
            'pharmacy_id' => 'required|integer|exists:pharmacies,id',
        ]);
        $items = $this->cart->items();
        $pharmacy = $this->availablePharmacies($items)->whereKey($data['pharmacy_id'])->first();
        if (! $pharmacy) throw ValidationException::withMessages(['pharmacy_id' => 'هذه الصيدلية لا يتوفر لديها كامل الطلب بالكميات المطلوبة. اختر صيدلية أخرى.']);

        $order = DB::transaction(function () use ($data, $items, $pharmacy) {
            $stocks = [];
            foreach ($items as $item) {
                $stock = DB::table('pharmacy_product')->where('pharmacy_id', $pharmacy->id)->where('product_id', $item->product->id)->lockForUpdate()->first();
                if (! $stock || ! $stock->is_allowed || ! $stock->is_available || $stock->quantity < $item->quantity) throw ValidationException::withMessages(['pharmacy_id' => 'تم تحديث مخزون الصيدلية. اختر صيدلية أخرى أو عدّل الكمية.']);
                $stocks[$item->product->id] = $stock;
                DB::table('pharmacy_product')->where('pharmacy_id', $pharmacy->id)->where('product_id', $item->product->id)->decrement('quantity', $item->quantity);
            }
            $order = Order::create($data + ['order_number' => $this->orderNumber(), 'user_id' => auth()->id(), 'pharmacy_id' => $pharmacy->id, 'total' => 0, 'status' => 'new']);
            $computedTotal = 0;
            foreach ($items as $item) {
                $stock = $stocks[$item->product->id];
                $pharmacyPrice = $stock->price !== null ? (float) $stock->price : (float) $item->product->price;
                $lineTotal = round($pharmacyPrice * $item->quantity, 2);
                $computedTotal += $lineTotal;
                OrderDetail::create(['order_id' => $order->id, 'user_id' => auth()->id(), 'product_id' => $item->product->id, 'product_name' => $item->product->name, 'price' => $pharmacyPrice, 'quantity' => $item->quantity, 'total' => $lineTotal]);
            }
            $order->update(['total' => round($computedTotal, 2)]);
            return $order;
        });
        app(AdminNotificationService::class)->orderPlaced($order->load('pharmacy'));
        $this->cart->clear();
        return redirect()->route('cart.success', $order->order_number);
    }

    public function success(string $order_number)
    {
        $order = Order::with(['order_details', 'pharmacy'])->where('order_number', $order_number)->firstOrFail();
        return view('site.cart.success', compact('order'));
    }

    protected function availablePharmacies($items)
    {
        $query = Pharmacy::orderable();
        foreach ($items as $item) {
            $query->whereHas('products', fn ($products) => $products->where('products.id', $item->product->id)->where('pharmacy_product.quantity', '>=', $item->quantity)->where('pharmacy_product.is_allowed', true)->where('pharmacy_product.is_available', true));
        }
        return $query->orderBy('id');
    }

    protected function orderNumber(): string
    {
        do { $number = 'ORD-' . date('Ymd') . '-' . mt_rand(1000, 9999); } while (Order::where('order_number', $number)->exists());
        return $number;
    }

    protected function payload(): array
    {
        $items = $this->cart->items();
        return ['count' => $this->cart->count(), 'total' => $this->cart->total(), 'prices_visible' => $this->cart->pricesVisible(), 'items' => $items->map(fn ($item) => [
            'id' => $item->product->id, 'name' => $item->product->trans_name, 'image' => $item->product->img_path,
            'price' => (float) $item->product->price, 'show_price' => (bool) $item->product->show_price,
            'quantity' => $item->quantity, 'total' => $item->total, 'url' => route('medicines.show', $item->product->id),
        ])->all()];
    }

    protected function respond(Request $request, string $message, string $type, int $status = 200)
    {
        if ($request->ajax() || $request->wantsJson()) return response()->json($this->payload() + ['message' => $message, 'type' => $type], $status);
        return redirect()->back()->with('cart_msg', $message)->with('cart_type', $type);
    }
}
