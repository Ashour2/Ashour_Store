<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pharmacy;
use App\Models\User;
use App\Services\AdminNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PharmacyController extends Controller
{
    public function index()
    {
        $pharmacies = Pharmacy::withCount(['products', 'orders', 'users'])
            ->withSum(['orders as sales_total' => fn ($orders) => $orders->where('status', 'delivered')], 'total')
            ->latest('id')->paginate(15);
        return view('admin.pharmacies.index', compact('pharmacies'));
    }

    public function create() { return view('admin.pharmacies.create', ['pharmacy' => new Pharmacy()]); }

    public function show(Pharmacy $pharmacy)
    {
        $products = $pharmacy->products()->with('category')->orderBy('products.id')->paginate(15);
        $orders = $pharmacy->orders()->latest('id')->take(10)->get();
        return view('admin.pharmacies.show', compact('pharmacy', 'products', 'orders'));
    }

    public function store(Request $request)
    {
        $pharmacy = Pharmacy::create($this->data($request));
        if ($request->ajax() || $request->wantsJson()) return response()->json(['id' => $pharmacy->id, 'name' => $pharmacy->trans_name, 'phone' => $pharmacy->phone, 'address' => $pharmacy->address, 'message' => __('admin.pharmacy_added')], 201);
        return redirect()->route('admin.pharmacies.index')->with('msg', __('admin.pharmacy_added'))->with('type', 'success');
    }

    public function edit(Pharmacy $pharmacy) { return view('admin.pharmacies.edit', compact('pharmacy')); }
    public function update(Request $request, Pharmacy $pharmacy) { $pharmacy->update($this->data($request)); return redirect()->route('admin.pharmacies.index')->with('msg', __('admin.pharmacy_updated'))->with('type', 'success'); }
public function destroy(Pharmacy $pharmacy)
    {
        DB::transaction(function () use ($pharmacy) {
            $userIds = $pharmacy->users()->pluck('id');

            // Close the staff accounts first: preserve past data but remove the
            // pharmacist role and branch link, so they cannot re-enter admin.
            if ($userIds->isNotEmpty()) {
                DB::table('sessions')->whereIn('user_id', $userIds)->delete();
                User::whereIn('id', $userIds)->update([
                    'pharmacy_id' => null,
                    'role_id' => null,
                    'type' => 'customer',
                    'remember_token' => Str::random(60),
                ]);
            }

            $pharmacy->delete();
        });

        return redirect()->route('admin.pharmacies.index')
            ->with('msg', 'تم حذف الصيدلية وإيقاف حسابات الصيادلة المرتبطة بها.')
            ->with('type', 'info');
    }

    protected function data(Request $request): array
    {
        $request->validate([
            'name_en' => 'required|string|max:100', 'name_ar' => 'required|string|max:100', 'phone' => 'nullable|string|max:30', 'address' => 'nullable|string|max:255',
            'subscription_plan' => 'nullable|string|max:60', 'subscription_status' => 'sometimes|required|in:active,trial,suspended', 'subscription_ends_at' => 'nullable|date',
        ]);
        return ['name' => json_encode(['en' => $request->name_en, 'ar' => $request->name_ar], JSON_UNESCAPED_UNICODE), 'phone' => $request->phone, 'address' => $request->address, 'is_active' => $request->boolean('is_active')] + $request->only(['subscription_plan', 'subscription_status', 'subscription_ends_at']);
    }
    public function subscriptions()
    {
        $pharmacies = Pharmacy::with(['users:id,name,pharmacy_id'])->withCount('users')->orderBy('subscription_ends_at')->paginate(20);
        return view('admin.pharmacies.subscriptions', compact('pharmacies'));
    }

    public function subscriptionAction(Request $request, Pharmacy $pharmacy)
    {
        $data = $request->validate(['action' => 'required|in:activate,renew,suspend']);
        $from = $pharmacy->subscription_ends_at && $pharmacy->subscription_ends_at->isFuture()
            ? $pharmacy->subscription_ends_at->copy() : now();

if ($data['action'] === 'suspend') {
            $pharmacy->update(['is_active' => false, 'subscription_status' => 'suspended']);
            app(AdminNotificationService::class)->subscriptionUpdated($pharmacy);
            return back()->with('msg', 'تم إيقاف اشتراك ' . $pharmacy->trans_name . '.')->with('type', 'info');
        }

        if ($data['action'] === 'renew') {
            $endsAt = $pharmacy->subscription_plan === 'annual' ? $from->addYear() : $from->addMonth();
            $message = 'تم تجديد اشتراك ' . $pharmacy->trans_name . '.';
        } else {
            $endsAt = $pharmacy->subscription_ends_at && $pharmacy->subscription_ends_at->isFuture()
                ? $pharmacy->subscription_ends_at : ($pharmacy->subscription_plan === 'annual' ? now()->addYear() : now()->addMonth());
            $message = 'تم تفعيل اشتراك ' . $pharmacy->trans_name . '.';
        }

        $pharmacy->update(['is_active' => true, 'subscription_status' => 'active', 'subscription_ends_at' => $endsAt]);
        app(AdminNotificationService::class)->subscriptionUpdated($pharmacy);
        return back()->with('msg', $message)->with('type', 'success');
    }
    public function updateSubscription(Request $request, Pharmacy $pharmacy)
    {
        $data = $request->validate([
            'subscription_plan' => 'nullable|string|max:60',
            'subscription_status' => 'required|in:active,trial,suspended',
            'subscription_ends_at' => 'nullable|date',
        ]);
        $pharmacy->update($data);
        app(AdminNotificationService::class)->subscriptionUpdated($pharmacy);
        return back()->with('msg', 'تم تحديث اشتراك ' . $pharmacy->trans_name . '.')->with('type', 'success');
    }
}
