<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Consultation;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->isWarehouse()) return redirect()->route('admin.warehouse.inventory');
        $products = Product::visibleTo($user);
        $orders = Order::query();
        if ($user->isPharmacist()) {
            $orders->where('pharmacy_id', $user->pharmacy_id);
        }

        $consultations = Consultation::query();
        if ($user->isPharmacist()) {
            $consultations->where('pharmacy_id', $user->pharmacy_id);
        }
        $stats = [
            'medicines' => $products->count(),
            'categories' => Category::count(),
            'consultations' => (clone $consultations)->count(),
            'consultations_new' => (clone $consultations)->where('status', 'new')->count(),
            'prescription' => Product::visibleTo($user)->where('requires_prescription', true)->count(),
            'out_of_stock' => $user->isPharmacist()
                ? Product::visibleTo($user)->whereHas('pharmacies', fn ($branches) => $branches->whereKey($user->pharmacy_id)
                    ->where(fn ($stock) => $stock->where('pharmacy_product.quantity', '<=', 0)->orWhere('pharmacy_product.is_available', false)))->count()
                : Product::where('quantity', '<=', 0)->count(),
            'users' => User::count(),
            'orders' => (clone $orders)->count(),
            'orders_new' => (clone $orders)->where('status', 'new')->count(),
        ];

        $latest_consultations = (clone $consultations)->with('product')->latest('id')->take(5)->get();
        $latest_medicines = Product::visibleTo($user)->latest('id')->take(5)->get();
        $latest_orders = (clone $orders)->with('pharmacy')->withCount('order_details')->latest('id')->take(5)->get();

        return view('admin.dashboard', compact('stats', 'latest_consultations', 'latest_medicines', 'latest_orders'));
    }

    public function loginType() { return view('auth.loginType'); }

    public function profile()
    {
        return view('admin.profile', ['admin' => Auth::user()]);
    }

    public function profile_data(Request $request)
    {
        $admin = Auth::user();
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($admin->id)],
            'current_password' => 'required',
            'password' => 'nullable|min:8|confirmed',
        ]);
        if (! Hash::check($request->current_password, $admin->password)) {
            return redirect()->back()->withErrors(['current_password' => __('admin.wrong_current_password')]);
        }
        $data = ['name' => $request->name, 'email' => $request->email];
        if ($request->filled('password')) $data['password'] = bcrypt($request->password);
        $admin->update($data);
        return redirect()->back()->with('msg', __('admin.account_updated'));
    }

    public function check_password(Request $request) { return Hash::check($request->password, Auth::user()->password); }
}
