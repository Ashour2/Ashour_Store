<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status'); $q = $request->input('q');
        $orders = $this->visibleOrders()->with('pharmacy')->withCount('order_details')
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($q, fn ($query) => $query->where(fn ($sub) => $sub->where('order_number', 'like', "%{$q}%")->orWhere('customer_name', 'like', "%{$q}%")->orWhere('customer_phone', 'like', "%{$q}%")))
            ->latest('id')->paginate(15)->withQueryString();
        $base = $this->visibleOrders();
        $counts = ['all' => (clone $base)->count()] + (clone $base)->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status')->toArray();
        return view('admin.orders.index', compact('orders', 'counts', 'status', 'q'));
    }

    public function show(Order $order)
    {
        $this->ensureVisible($order); $order->load(['order_details.product', 'pharmacy']);
        return view('admin.orders.show', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $this->ensureVisible($order);
        $data = $request->validate(['status' => 'required|in:' . implode(',', Order::STATUSES), 'note' => 'nullable|string|max:1000']);
        $order->update($data);
        return redirect()->route('admin.orders.show', $order->id)->with('msg', __('admin.order_updated'))->with('type', 'success');
    }

    public function destroy(Order $order)
    {
        $this->ensureVisible($order); $order->delete();
        return redirect()->route('admin.orders.index')->with('msg', __('admin.order_deleted'))->with('type', 'info');
    }

    private function visibleOrders()
    {
        $query = Order::query(); $user = auth()->user();
        if ($user->isPharmacist()) $query->where('pharmacy_id', $user->pharmacy_id);
        return $query;
    }

    private function ensureVisible(Order $order): void
    {
        if (auth()->user()->isPharmacist()) abort_unless((int) $order->pharmacy_id === (int) auth()->user()->pharmacy_id, 403);
    }
}