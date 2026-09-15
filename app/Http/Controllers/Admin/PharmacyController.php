<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pharmacy;
use Illuminate\Http\Request;

class PharmacyController extends Controller
{
    public function index()
    {
        $pharmacies = Pharmacy::withCount('products')->latest('id')->paginate(15);

        return view('admin.pharmacies.index', compact('pharmacies'));
    }

    public function create()
    {
        $pharmacy = new Pharmacy();

        return view('admin.pharmacies.create', compact('pharmacy'));
    }

    /**
     * Medicines held at one branch, with the quantity of each.
     */
    public function show(Pharmacy $pharmacy)
    {
        $products = $pharmacy->products()
            ->with('category')
            ->orderBy('products.id')
            ->paginate(15);

        return view('admin.pharmacies.show', compact('pharmacy', 'products'));
    }

    public function store(Request $request)
    {
        $pharmacy = Pharmacy::create($this->data($request));

        // The medicine form adds a branch inline without leaving the page
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'id'      => $pharmacy->id,
                'name'    => $pharmacy->trans_name,
                'phone'   => $pharmacy->phone,
                'address' => $pharmacy->address,
                'message' => __('admin.pharmacy_added'),
            ], 201);
        }

        return redirect()
            ->route('admin.pharmacies.index')
            ->with('msg', __('admin.pharmacy_added'))
            ->with('type', 'success');
    }

    public function edit(Pharmacy $pharmacy)
    {
        return view('admin.pharmacies.edit', compact('pharmacy'));
    }

    public function update(Request $request, Pharmacy $pharmacy)
    {
        $pharmacy->update($this->data($request));

        return redirect()
            ->route('admin.pharmacies.index')
            ->with('msg', __('admin.pharmacy_updated'))
            ->with('type', 'success');
    }

    public function destroy(Pharmacy $pharmacy)
    {
        $pharmacy->delete();

        return redirect()
            ->route('admin.pharmacies.index')
            ->with('msg', __('admin.pharmacy_deleted'))
            ->with('type', 'info');
    }

    /** Validate and shape the bilingual branch payload. */
    protected function data(Request $request): array
    {
        $request->validate([
            'name_en'  => 'required|string|max:100',
            'name_ar'  => 'required|string|max:100',
            'phone'    => 'nullable|string|max:30',
            'address'  => 'nullable|string|max:255',
        ]);

        return [
            'name' => json_encode([
                'en' => $request->name_en,
                'ar' => $request->name_ar,
            ], JSON_UNESCAPED_UNICODE),
            'phone'     => $request->phone,
            'address'   => $request->address,
            'is_active' => $request->boolean('is_active'),
        ];
    }
}
