<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Product;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    /**
     * Public consultation request form. Optionally pre-linked to a medicine.
     */
    public function create(Request $request)
    {
        $product = null;
        if ($request->filled('medicine')) {
            $product = Product::find($request->input('medicine'));
        }

        return view('site.consultation', compact('product'));
    }

    /**
     * Store a consultation request from the public site.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
            'product_id' => 'nullable|exists:products,id',
            'message' => 'required|string',
        ]);

        Consultation::create($data);

        return redirect()
            ->back()
            ->with('consultation_sent', true);
    }
}
