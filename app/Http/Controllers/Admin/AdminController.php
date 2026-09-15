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
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{

public function index(){
    $stats = [
        'medicines'          => Product::count(),
        'categories'         => Category::count(),
        'consultations'      => Consultation::count(),
        'consultations_new'  => Consultation::where('status', 'new')->count(),
        'prescription'       => Product::where('requires_prescription', true)->count(),
        'out_of_stock'       => Product::where('quantity', '<=', 0)->count(),
        'users'              => User::count(),
        'orders'             => Order::count(),
        'orders_new'         => Order::where('status', 'new')->count(),
    ];

    $latest_consultations = Consultation::with('product')->latest('id')->take(5)->get();
    $latest_medicines = Product::latest('id')->take(5)->get();
    $latest_orders = Order::withCount('order_details')->latest('id')->take(5)->get();

    return view('admin.dashboard', compact('stats', 'latest_consultations', 'latest_medicines', 'latest_orders'));
}
public function loginType(){
    return view('auth.loginType');
}


    function profile() {
        $admin = Auth::user();

        return view('admin.profile', compact('admin'));
    }

    /**
     * The signed-in manager's own account: name, sign-in email and password.
     * The current password is required for any of it, since changing the email
     * changes how this account signs in.
     */
    function profile_data(Request $request) {
        /** @var User $admin */
        $admin = Auth::user();

        $request->validate([
            'name'             => 'required|string|max:100',
            'email'            => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($admin->id)],
            'current_password' => 'required',
            'password'         => 'nullable|min:8|confirmed',
        ]);

        if (! Hash::check($request->current_password, $admin->password)) {
            return redirect()->back()->withErrors(['current_password' => __('admin.wrong_current_password')]);
        }

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
        ];

        // An empty password box means "keep the current one"
        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $admin->update($data);

        return redirect()->back()->with('msg', __('admin.account_updated'));
    }

    function check_password(Request $request) {
        return Hash::check($request->password, Auth::user()->password);
    }

    // function orders() {
    //     if(request()->has('id')) {
    //         $id = request()->id;
    //         Auth::user()->notifications->find($id)->markAsRead();
    //     }

    //     return 'Order page';
    // }

}
