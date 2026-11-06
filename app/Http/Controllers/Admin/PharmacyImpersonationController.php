<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pharmacy;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PharmacyImpersonationController extends Controller
{
    public function start(Request $request, Pharmacy $pharmacy)
    {
        abort_unless($request->user()->isSuperAdmin(), 403);
        $pharmacist = User::where('pharmacy_id', $pharmacy->id)->whereHas('role', fn ($role) => $role->where('slug', 'pharmacist'))->firstOrFail();
        $request->session()->put('impersonator_id', $request->user()->id);
        Auth::login($pharmacist);
        return redirect()->route('admin.index')->with('msg', 'أنت الآن داخل حساب ' . $pharmacy->trans_name . '.');
    }

    public function leave(Request $request)
    {
        $id = $request->session()->pull('impersonator_id'); abort_unless($id, 403);
        Auth::login(User::findOrFail($id));
        return redirect()->route('admin.pharmacies.index')->with('msg', 'تم الرجوع إلى حساب المدير.');
    }
}