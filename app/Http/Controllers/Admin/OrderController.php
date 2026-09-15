<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Orders inbox, newest first, optionally filtered by status or search.
     */
    public function index(Request $request)
    {
        $status = $request->input('status');
        $q = $request->input('q');

        $orders = Order::withCount('order_details')
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('order_number', 'like', "%{$q}%")
                        ->orWhere('customer_name', 'like', "%{$q}%")
                        ->orWhere('customer_phone', 'like', "%{$q}%");
                });
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $counts = [
            'all' => Order::count(),
        ] + Order::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        return view('admin.orders.index', compact('orders', 'counts', 'status', 'q'));
    }

    /**
     * Full order with its lines, the status form and the WhatsApp hand-off.
     */
    public function show(Order $order)
    {
        $order->load('order_details.product');

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Move the order along its lifecycle (new → confirmed → delivered).
     */
    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => 'required|in:' . implode(',', Order::STATUSES),
            'note'   => 'nullable|string|max:1000',
        ]);

        $order->update($data);

        return redirect()
            ->route('admin.orders.show', $order->id)
            ->with('msg', __('admin.order_updated'))
            ->with('type', 'success');
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()
            ->route('admin.orders.index')
            ->with('msg', __('admin.order_deleted'))
            ->with('type', 'info');
    }
}
