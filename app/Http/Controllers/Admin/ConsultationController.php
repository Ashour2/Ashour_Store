<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    /**
     * List consultation requests (newest first).
     */
    public function index()
    {
        $consultations = Consultation::with('product')->latest('id')->paginate(15);
        $new_count = Consultation::where('status', 'new')->count();

        return view('admin.consultations.index', compact('consultations', 'new_count'));
    }

    /**
     * Show a single consultation and its reply form.
     */
    public function show(Consultation $consultation)
    {
        return view('admin.consultations.show', compact('consultation'));
    }

    /**
     * Save the pharmacist reply and mark as answered.
     */
    public function update(Request $request, Consultation $consultation)
    {
        $request->validate([
            'reply' => 'required',
        ]);

        $consultation->update([
            'reply' => $request->reply,
            'status' => 'answered',
        ]);

        return redirect()
            ->route('admin.consultations.index')
            ->with('msg', 'Reply saved successfully')
            ->with('type', 'success');
    }

    /**
     * Delete a consultation.
     */
    public function destroy(Consultation $consultation)
    {
        $consultation->delete();

        return redirect()
            ->route('admin.consultations.index')
            ->with('msg', 'Consultation deleted successfully')
            ->with('type', 'info');
    }
}
