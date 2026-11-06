<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Pharmacy;
use App\Models\Role;
use App\Models\User;
use App\Services\AdminNotificationService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(Request $request): View
    {
        return view('auth.register', ['isPharmacist' => $request->query('type') === 'pharmacist']);
    }

    public function store(Request $request): RedirectResponse
    {
        $isPharmacist = $request->input('account_type') === 'pharmacist';
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [($isPharmacist ? 'nullable' : 'required'), 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];
        if ($isPharmacist) {
            $rules += [
                'pharmacy_name' => ['required', 'string', 'max:120'],
                'phone' => ['required', 'string', 'max:30'],
                'address' => ['required', 'string', 'max:255'],
                'subscription_plan' => ['required', 'in:monthly,annual'],
            ];
        }
        $request->validate($rules);

        if (! $isPharmacist) {
            $user = User::create(['name' => $request->name, 'email' => $request->email, 'password' => Hash::make($request->password)]);
            event(new Registered($user));
            Auth::login($user);
            return redirect(route('admin.index', absolute: false));
        }

        [$user, $pharmacy] = DB::transaction(function () use ($request) {
            $pharmacy = Pharmacy::create([
                'name' => json_encode(['ar' => $request->pharmacy_name, 'en' => $request->pharmacy_name], JSON_UNESCAPED_UNICODE),
                'phone' => $request->phone,
                'address' => $request->address,
                'is_active' => true,
                'subscription_plan' => $request->subscription_plan,
                'subscription_status' => 'trial',
                'subscription_ends_at' => now()->addMonth(),
            ]);
            $role = Role::where('slug', Role::PHARMACIST)->firstOrFail();
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email ?: ('pharmacy.' . $pharmacy->id . '@pending.linepharm.local'),
                'password' => Hash::make($request->password),
                'type' => 'employee',
                'role_id' => $role->id,
                'pharmacy_id' => $pharmacy->id,
            ]);
            return [$user, $pharmacy];
        });

        app(AdminNotificationService::class)->forManagers(
            'subscription',
            'طلب اشتراك صيدلية جديد',
            "سجلت {$pharmacy->trans_name} في الخطة " . ($pharmacy->subscription_plan === 'annual' ? 'السنوية (100$)' : 'الشهرية (10$)') . ' — تجربة مجانية لمدة شهر.',
            route('admin.pharmacies.show', $pharmacy)
        );

        event(new Registered($user));
        Auth::login($user);
        return redirect()->route('admin.index')->with('msg', 'تم إنشاء حساب صيدليتك وتفعيل التجربة المجانية لمدة شهر. سيتواصل معك المدير بخصوص الاشتراك.')->with('type', 'success');
    }
}