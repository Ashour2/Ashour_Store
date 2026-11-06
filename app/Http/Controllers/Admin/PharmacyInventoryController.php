<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pharmacy;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PharmacyInventoryController extends Controller
{
    public function index(Request $request)
    {
        $pharmacy = $this->pharmacy();
        $q = trim((string) $request->input('q'));
        $products = Product::with('category')
            ->whereDoesntHave('pharmacies', fn ($pharmacies) => $pharmacies
                ->where('pharmacies.id', $pharmacy->id)
                ->where('pharmacy_product.is_allowed', false))
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($search) use ($q) {
                    $search->where('name', 'like', "%{$q}%")
                        ->orWhere('active_ingredient', 'like', "%{$q}%")
                        ->orWhere('scientific_name', 'like', "%{$q}%")
                        ->orWhere('trade_name', 'like', "%{$q}%")
                        ->orWhere('barcode', 'like', "%{$q}%");
                });
            })
            ->orderBy('id')->paginate(25)->withQueryString();
        $inventory = $pharmacy->products()->whereIn('products.id', $products->modelKeys())->get();
        $held = $inventory->pluck('pivot.quantity', 'id');
        $prices = $inventory->pluck('pivot.price', 'id');
        $priceVisibility = $inventory->pluck('pivot.show_price', 'id');
        $availability = $inventory->pluck('pivot.is_available', 'id');
        return view('admin.pharmacies.my-inventory', compact('pharmacy', 'products', 'held', 'prices', 'priceVisibility', 'availability', 'q'));
    }

    public function attach(Request $request, Product $product)
    {
        $data = $request->validate([
            'quantity' => 'nullable|integer|min:0|max:1000000',
            'price' => 'nullable|numeric|min:0|max:999999.99',
            'show_price' => 'nullable|boolean',
            'is_available' => 'sometimes|boolean',
            'pharmacy_notes' => 'nullable|string|max:2000',
        ]);
        $pharmacy = $this->pharmacy();
        abort_if(DB::table('pharmacy_product')->where('pharmacy_id', $pharmacy->id)->where('product_id', $product->id)->where('is_allowed', false)->exists(), 403);
        $changes = ['is_allowed' => true];
        foreach (['quantity', 'price', 'show_price', 'is_available', 'pharmacy_notes'] as $field) {
            if (array_key_exists($field, $data)) {
                $changes[$field === 'pharmacy_notes' ? 'notes' : $field] = $data[$field];
            }
        }
        if (array_key_exists('quantity', $changes)) $changes['quantity'] = (int) $changes['quantity'];
        $pharmacy->products()->syncWithoutDetaching([$product->id => $changes]);
        return back()->with('msg', 'تم حفظ سعر الدواء وكميته وحالة توفره في صيدليتك.')->with('type', 'success');
    }

    /** Remove only the pharmacy inventory relation, never the shared catalogue item. */
    public function detach(Product $product)
    {
        $pharmacy = $this->pharmacy();
        $pharmacy->products()->detach($product->id);

        return back()->with('msg', 'تمت إزالة الدواء من أدويتي فقط.')->with('type', 'info');
    }
    public function attachAll()
    {
        $pharmacy = $this->pharmacy(); $now = now();
        Product::select('id')->orderBy('id')->chunkById(500, function ($products) use ($pharmacy, $now) {
            $rows = $products->map(fn ($product) => ['pharmacy_id' => $pharmacy->id, 'product_id' => $product->id, 'quantity' => 0, 'created_at' => $now, 'updated_at' => $now])->all();
            DB::table('pharmacy_product')->insertOrIgnore($rows);
        });
        return back()->with('msg', 'تمت إضافة جميع الأدوية إلى صيدليتك بكمية صفر. حدّث الكميات من قائمة الأدوية.')->with('type', 'success');
    }

    private function pharmacy(): Pharmacy
    {
        $user = auth()->user();
        abort_unless($user->isPharmacist() && $user->pharmacy_id, 403);
        return Pharmacy::findOrFail($user->pharmacy_id);
    }
}
