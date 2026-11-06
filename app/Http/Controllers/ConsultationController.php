<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Product;
use App\Models\Pharmacy;
use App\Services\AdminNotificationService;
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

        $pharmacies = Pharmacy::active()->orderBy('id')->get();
        return view('site.consultation', compact('product', 'pharmacies'));
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
            'pharmacy_id' => 'nullable|exists:pharmacies,id',
            'message' => 'required|string',
            'prescription' => 'nullable|mimes:jpg,jpeg,png,webp,pdf|max:4096',
        ]);

        // Store the uploaded prescription (image or PDF) if provided
        if ($request->hasFile('prescription')) {
            $dir = public_path('prescriptions');
            if (! is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            $file = $request->file('prescription');
            $filename = 'rx_' . time() . '_' . mt_rand(1000, 9999) . '.' . $file->getClientOriginalExtension();
            $file->move($dir, $filename);
            $data['prescription'] = $filename;
        }

        if (empty($data['pharmacy_id']) && ! empty($data['product_id'])) {
            $data['pharmacy_id'] = Product::find($data['product_id'])?->pharmacies()
                ->where('pharmacies.is_active', true)->orderBy('pharmacies.id')->value('pharmacies.id');
        }

        $consultation = Consultation::create($data);
        app(AdminNotificationService::class)->consultationCreated($consultation);

        return redirect()
            ->back()
            ->with('consultation_sent', true);
    }
}
