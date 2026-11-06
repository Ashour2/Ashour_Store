<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Warehouse, WholesaleOrder};
use App\Services\WholesaleOrderService;
use Illuminate\Http\Request;

class WholesaleController extends Controller
{
    public function catalog()
    {
        $this->pharmacist();
        $warehouses = Warehouse::where('is_active', true)->withCount(['stocks' => fn ($stocks) => $stocks
            ->where('is_available', true)->where('quantity', '>', 0)->whereHas('product', fn ($products) => $products->publiclyVisible())])->orderBy('name')->paginate(15);
        return view('admin.warehouses.catalog', compact('warehouses'));
    }

    public function show(Request $request, Warehouse $warehouse)
    {
        $this->pharmacist();
        abort_unless($warehouse->is_active, 404);
        $q = trim((string) $request->input('q'));
        $stocks = $warehouse->stocks()->with('product.category')->where('is_available', true)->where('quantity', '>', 0)
            ->whereHas('product', fn ($products) => $products->publiclyVisible()->when($q !== '', fn ($search) => $search->where('name', 'like', "%{$q}%")))
            ->orderBy('id')->paginate(25)->withQueryString();
        return view('admin.warehouses.show', compact('warehouse', 'stocks', 'q'));
    }

    public function store(Request $request, Warehouse $warehouse, WholesaleOrderService $service)
    {
        $this->pharmacist();
        $data = $request->validate(['quantities' => 'required|array|max:100', 'quantities.*' => 'nullable|integer|min:0|max:1000000',
            'request_token' => 'required|uuid', 'delivery_address' => 'required|string|max:255', 'notes' => 'nullable|string|max:2000']);
        $order = $service->place(auth()->user(), $warehouse, $data);
        return redirect()->route('admin.wholesale.orders.show', $order)->with('msg', 'تم إرسال طلبك للمستودع. الدفع والتسوية خارج المنصة.');
    }

    public function orders()
    {
        $query = $this->scopedOrders();
        $orders = $query->with('warehouse')->latest('id')->paginate(20);
        return view('admin.warehouses.orders', compact('orders'));
    }

    public function order(WholesaleOrder $order)
    {
        abort_unless($this->scopedOrders()->whereKey($order->id)->exists(), 403);
        $order->load('items', 'warehouse');
        return view('admin.warehouses.order', compact('order'));
    }

    public function status(Request $request, WholesaleOrder $order, WholesaleOrderService $service)
    {
        abort_unless($this->scopedOrders()->whereKey($order->id)->exists(), 403);
        $data = $request->validate(['status' => 'required|in:accepted,preparing,completed,cancelled']);
        $service->transition(auth()->user(), $order, $data['status']);
        return back()->with('msg', 'تم تحديث حالة الطلب.');
    }

    private function pharmacist(): void
    {
        abort_unless(auth()->user()->isPharmacist() && auth()->user()->pharmacy_id, 403);
    }

    private function scopedOrders()
    {
        $user = auth()->user();
        if ($user->isWarehouse() && $user->warehouse_id) return WholesaleOrder::where('warehouse_id', $user->warehouse_id);
        if ($user->isPharmacist() && $user->pharmacy_id) return WholesaleOrder::where('pharmacy_id', $user->pharmacy_id);
        abort_unless($user->can('warehouses.manage'), 403);
        return WholesaleOrder::query();
    }
}
