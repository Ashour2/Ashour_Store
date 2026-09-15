<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function __construct(protected CartService $cart)
    {
    }

    /**
     * Cart contents for the floating panel. Returns JSON for the AJAX panel
     * and a full page when opened directly.
     */
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($this->payload());
        }

        return view('site.cart.index', ['items' => $this->cart->items()]);
    }

    /**
     * Add a medicine to the cart from the card or the details page.
     */
    public function add(Request $request, Product $medicine)
    {
        $request->validate(['quantity' => 'nullable|integer|min:1|max:99']);

        if ($medicine->show_stock && ! $medicine->in_stock) {
            return $this->respond($request, __('site.cart_out_of_stock'), 'danger', 422);
        }

        $this->cart->add($medicine, (int) $request->input('quantity', 1));

        return $this->respond($request, __('site.cart_added'), 'success');
    }

    /**
     * Set an exact quantity for one line (the +/- buttons in the panel).
     */
    public function update(Request $request, Product $medicine)
    {
        $request->validate(['quantity' => 'required|integer|min:0|max:99']);

        $this->cart->update($medicine, (int) $request->quantity);

        return $this->respond($request, __('site.cart_updated'), 'success');
    }

    public function remove(Request $request, int $medicine)
    {
        $this->cart->remove($medicine);

        return $this->respond($request, __('site.cart_removed'), 'info');
    }

    public function clear(Request $request)
    {
        $this->cart->clear();

        return $this->respond($request, __('site.cart_cleared'), 'info');
    }

    /**
     * Checkout form: customer contact details for the order.
     */
    public function checkout()
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('medicines.index')->with('cart_msg', __('site.cart_empty'));
        }

        return view('site.cart.checkout', [
            'items' => $this->cart->items(),
            'total' => $this->cart->total(),
        ]);
    }

    /**
     * Store the order so it lands in the dashboard, then send the customer to
     * the confirmation page which hands the summary over to WhatsApp.
     */
    public function store(Request $request)
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('medicines.index')->with('cart_msg', __('site.cart_empty'));
        }

        $data = $request->validate([
            'customer_name'    => 'required|string|max:100',
            'customer_phone'   => 'required|string|max:30',
            'customer_address' => 'nullable|string|max:255',
            'note'             => 'nullable|string|max:1000',
        ]);

        $items = $this->cart->items();

        $order = DB::transaction(function () use ($data, $items) {
            $order = Order::create($data + [
                'order_number' => $this->orderNumber(),
                'user_id'      => auth()->id(),
                'total'        => round($items->sum('total'), 2),
                'status'       => 'new',
            ]);

            foreach ($items as $item) {
                OrderDetail::create([
                    'order_id'     => $order->id,
                    'user_id'      => auth()->id(),
                    'product_id'   => $item->product->id,
                    'product_name' => $item->product->name,
                    'price'        => $item->product->price,
                    'quantity'     => $item->quantity,
                    'total'        => $item->total,
                ]);
            }

            return $order;
        });

        $this->cart->clear();

        return redirect()->route('cart.success', $order->order_number);
    }

    /**
     * Thank-you page with the WhatsApp hand-off for the placed order.
     */
    public function success(string $order_number)
    {
        $order = Order::with('order_details')->where('order_number', $order_number)->firstOrFail();

        return view('site.cart.success', compact('order'));
    }

    /** Short, human readable order reference, e.g. ORD-20260914-4821. */
    protected function orderNumber(): string
    {
        do {
            $number = 'ORD-' . date('Ymd') . '-' . mt_rand(1000, 9999);
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }

    /** Cart state shared by every JSON response. */
    protected function payload(): array
    {
        $items = $this->cart->items();

        return [
            'count'  => $this->cart->count(),
            'total'  => $this->cart->total(),
            'prices_visible' => $this->cart->pricesVisible(),
            'items'  => $items->map(fn ($item) => [
                'id'         => $item->product->id,
                'name'       => $item->product->trans_name,
                'image'      => $item->product->img_path,
                'price'      => (float) $item->product->price,
                'show_price' => (bool) $item->product->show_price,
                'quantity'   => $item->quantity,
                'total'      => $item->total,
                'url'        => route('medicines.show', $item->product->id),
            ])->all(),
        ];
    }

    /** AJAX calls get the fresh cart, plain form posts get a redirect back. */
    protected function respond(Request $request, string $message, string $type, int $status = 200)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($this->payload() + [
                'message' => $message,
                'type'    => $type,
            ], $status);
        }

        return redirect()->back()->with('cart_msg', $message)->with('cart_type', $type);
    }
}
