<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    private function visibleConsultations(Request $request)
    {
        $query = Consultation::with(['product', 'pharmacy'])->latest('id');
        if ($request->user()->isPharmacist()) {
            $query->where('pharmacy_id', $request->user()->pharmacy_id);
        }
        return $query;
    }

    private function ensureVisible(Request $request, Consultation $consultation): void
    {
        abort_unless(! $request->user()->isPharmacist() || $consultation->pharmacy_id === $request->user()->pharmacy_id, 403);
    }

    public function index(Request $request)
    {
        $query = $this->visibleConsultations($request);
        $consultations = $query->paginate(15);
        $new_count = (clone $query)->where('status', 'new')->count();
        return view('admin.consultations.index', compact('consultations', 'new_count'));
    }

    public function show(Request $request, Consultation $consultation)
    {
        $this->ensureVisible($request, $consultation);
        return view('admin.consultations.show', compact('consultation'));
    }

    public function update(Request $request, Consultation $consultation)
    {
        $this->ensureVisible($request, $consultation);
        $request->validate(['reply' => 'required']);
        $consultation->update(['reply' => $request->reply, 'status' => 'answered']);
        return redirect()->route('admin.consultations.index')->with('msg', 'Reply saved successfully')->with('type', 'success');
    }

    public function destroy(Request $request, Consultation $consultation)
    {
        $this->ensureVisible($request, $consultation);
        $consultation->delete();
        return redirect()->route('admin.consultations.index')->with('msg', 'Consultation deleted successfully')->with('type', 'info');
    }
}