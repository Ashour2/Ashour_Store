<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Image;
use App\Models\Pharmacy;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::visibleTo($this->user())
            ->with(['pharmacies', 'addedBy'])
            ->withCount('pharmacies')
            ->latest('id')
            ->paginate(10);

        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->requirePharmacyForPharmacist();
        $categories = Category::select('id', 'name')->get();
        $product = new Product();

        return view('admin.products.create', compact('categories', 'product') + $this->branches($product));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->requirePharmacyForPharmacist();
        $request->validate([
            'name_en' => 'required',
            'name_ar' => 'required',
            'image' => 'required',
            'gallery' => 'nullable',
            'price' => 'required',
            'description_en' => 'required',
            'description_ar' => 'required',
            'quantity' => 'required',
            'category_id' => 'required',
            'scientific_name' => 'nullable|string|max:255',
            'trade_name' => 'nullable|string|max:255',
            'contraindications_warnings_en' => 'nullable|string',
            'contraindications_warnings_ar' => 'nullable|string',
            'pharmacies' => 'array',
            'pharmacies.*' => 'integer',
            'quantities' => 'array',
            'quantities.*' => 'nullable|integer|min:0|max:1000000',
        ]);

        // $data = $request->except('_token', 'image', 'gallery');

        $name = [
            'en' => $request->name_en,
            'ar' => $request->name_ar,
        ];

        $desc = [
            'en' => $request->description_en,
            'ar' => $request->description_ar,
        ];

        $warnings = [
            'en' => $request->contraindications_warnings_en,
            'ar' => $request->contraindications_warnings_ar,
        ];

        $product = Product::create([
            // 'name' => $request->name,
            // 'description' => $request->description,
            'name'=> json_encode($name,JSON_UNESCAPED_UNICODE),
            'description'=> json_encode($desc,JSON_UNESCAPED_UNICODE),
            'scientific_name' => $request->scientific_name,
            'trade_name' => $request->trade_name,
            'contraindications_warnings' => json_encode($warnings, JSON_UNESCAPED_UNICODE),

            'price' => $request->price,
            'show_price' => $request->boolean('show_price'),
            'quantity' => $request->quantity,
            'stock_display' => $request->stock_display ?? 'quantity',
            'category_id' => $request->category_id,
            'added_by_id' => $this->user()->id,
            'active_ingredient' => $request->active_ingredient,
            'dosage_form' => $request->dosage_form,
            'requires_prescription' => $request->boolean('requires_prescription'),
            'barcode' => $request->barcode,
            'expiry_date' => $request->expiry_date,
        ]);

        // Which branches stock it, and how many units each holds
        $this->syncProductPharmacies($product, $request);

        // Add image to relation
        $img_name = rand().time().$request->file('image')->getClientOriginalName();
        $request->file('image')->move(public_path('images'), $img_name);
        $product->image()->create([
            'path' => $img_name,
        ]);

        if($request->has('gallery')) {
            foreach($request->gallery as $img) {
                $img_name = rand().time().$img->getClientOriginalName();
                $img->move(public_path('images'), $img_name);
                $product->image()->create([
                    'path' => $img_name,
                    'type' => 'gallery'
                ]);
            }
        }

        return redirect()
        ->route('admin.products.index')
        ->with('msg', 'Product added successfully')
        ->with('type', 'success');
    }

    /**
     * Branches and this medicine's current quantity at each, for the form.
     */
    protected function branches(Product $product): array
    {
        $user = $this->user();
        $pharmacies = $user->isPharmacist()
            ? Pharmacy::whereKey($user->pharmacy_id)->get()
            : Pharmacy::orderByDesc('is_active')->orderBy('id')->get();

        return [
            'pharmacies' => $pharmacies,
            'held' => $product->exists
                ? $product->pharmacies()->pluck('quantity', 'pharmacies.id')
                : collect(),
        ];
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $this->ensureVisibleToUser($product);
        $categories = Category::select('id', 'name')->get();

        return view('admin.products.edit', compact('product', 'categories') + $this->branches($product));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $this->ensureVisibleToUser($product);
        // dd($request->all());
        $request->validate([
            'name_en' => 'required',
            'name_ar' => 'required',
            'price' => 'required',
            'description_en' => 'required',
            'description_ar' => 'required',
            'quantity' => 'required',
            'category_id' => 'required',
            'scientific_name' => 'nullable|string|max:255',
            'trade_name' => 'nullable|string|max:255',
            'contraindications_warnings_en' => 'nullable|string',
            'contraindications_warnings_ar' => 'nullable|string',
            'pharmacies' => 'array',
            'pharmacies.*' => 'integer',
            'quantities' => 'array',
            'quantities.*' => 'nullable|integer|min:0|max:1000000',
        ]);

        $name = [
            'en' => $request->name_en,
            'ar' => $request->name_ar,
        ];

        $desc = [
            'en' => $request->description_en,
            'ar' => $request->description_ar,
        ];

        $warnings = [
            'en' => $request->contraindications_warnings_en,
            'ar' => $request->contraindications_warnings_ar,
        ];

        $product->update([
            'name' => json_encode($name, JSON_UNESCAPED_UNICODE),
            'description' => json_encode($desc, JSON_UNESCAPED_UNICODE),
            'scientific_name' => $request->scientific_name,
            'trade_name' => $request->trade_name,
            'contraindications_warnings' => json_encode($warnings, JSON_UNESCAPED_UNICODE),
            'price' => $request->price,
            'show_price' => $request->boolean('show_price'),
            'quantity' => $request->quantity,
            'stock_display' => $request->stock_display ?? 'quantity',
            'category_id' => $request->category_id,
            'active_ingredient' => $request->active_ingredient,
            'dosage_form' => $request->dosage_form,
            'requires_prescription' => $request->boolean('requires_prescription'),
            'barcode' => $request->barcode,
            'expiry_date' => $request->expiry_date,
        ]);

        $this->syncProductPharmacies($product, $request);

        // Add image to relation
        if($request->hasFile('image')) {
            if($product->image) {
                File::delete( public_path('images/'.$product->image->path) );
            }

            $product->image()->delete();

            $img_name = rand().time().$request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('images'), $img_name);
            $product->image()->create([
                'path' => $img_name,
            ]);
        }


        if($request->has('gallery')) {
            foreach($request->gallery as $img) {
                $img_name = rand().time().$img->getClientOriginalName();
                $img->move(public_path('images'), $img_name);
                $product->image()->create([
                    'path' => $img_name,
                    'type' => 'gallery'
                ]);
            }
        }

        return redirect()
        ->route('admin.products.index')
        ->with('msg', 'Product updated successfully')
        ->with('type', 'info');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $this->ensureVisibleToUser($product);
        File::delete( public_path('images/'.$product->image->path) );
        foreach($product->gallery as $img ) {
            File::delete( public_path('images/'.$img->path) );
        }

        $product->image()->delete();
        $product->gallery()->delete();
        $product->delete();

        return redirect()
        ->route('admin.products.index')
        ->with('msg', 'Product deleted successfully')
        ->with('type', 'info');
    }

    function delete_img($id) {
        $img = Image::findOrFail($id);
        if ($img->imageable instanceof Product) {
            $this->ensureVisibleToUser($img->imageable);
        }
        File::delete( public_path('images/'.$img->path) );
        return Image::destroy($id);
    }

    protected function user()
    {
        return auth()->user();
    }

    /** A pharmacist must be assigned to a branch before working with medicines. */
    protected function requirePharmacyForPharmacist(): void
    {
        abort_if($this->user()->isPharmacist() && ! $this->user()->pharmacy_id, 403,
            __('admin.pharmacy_assignment_required'));
    }

    /** Deny direct URLs and forged requests for a medicine outside the branch. */
    protected function ensureVisibleToUser(Product $product): void
    {
        $user = $this->user();
        if ($user->isPharmacist()) {
            $this->requirePharmacyForPharmacist();
            abort_unless($product->pharmacies()->whereKey($user->pharmacy_id)->exists(), 403);
        }
    }

    /** A pharmacist cannot choose or modify any branch other than their own. */
    protected function syncProductPharmacies(Product $product, Request $request): void
    {
        $user = $this->user();
        if ($user->isPharmacist()) {
            $product->syncBranchStock([
                $user->pharmacy_id => (int) $request->quantity,
            ], [$user->pharmacy_id]);
            return;
        }

        $product->syncBranchStock($request->input('quantities', []), $request->input('pharmacies', []));
    }
}
