<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pharmacy;
use App\Models\Product;
use Illuminate\Http\Request;

/**
 * Where a medicine physically sits: which branches stock it and how many units
 * each one holds. Manager-only information, reachable from the medicines list.
 */
class ProductStockController extends Controller
{
    /**
     * Branch-by-branch stock sheet for one medicine.
     */
    public function edit(Product $product)
    {
        $product->load('pharmacies');

        // Every branch is listed so the manager can stock a new one in place;
        // the ones already holding it come pre-filled with their quantity.
        $held = $product->pharmacies->pluck('pivot.quantity', 'id');
        $pharmacies = Pharmacy::orderByDesc('is_active')->orderBy('id')->get();

        return view('admin.products.stock', compact('product', 'pharmacies', 'held'));
    }

    /**
     * Save the quantities. A branch left empty or at zero stops stocking the
     * medicine rather than keeping a meaningless zero row.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'quantities'   => 'array',
            'quantities.*' => 'nullable|integer|min:0|max:1000000',
        ]);

        $product->syncBranchStock($request->input('quantities', []));

        return redirect()
            ->route('admin.products.stock.edit', $product->id)
            ->with('msg', __('admin.stock_saved'))
            ->with('type', 'success');
    }
}
