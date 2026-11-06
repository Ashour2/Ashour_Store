<?php

namespace App\Services;

use App\Models\AdminNotification;
use App\Models\Consultation;
use App\Models\Order;
use App\Models\Pharmacy;
use App\Models\User;

class AdminNotificationService
{
    public function orderPlaced(Order $order): void
    {
        $url = route('admin.orders.show', $order);
        $pharmacyName = $order->pharmacy?->trans_name ?: 'الصيدلية المختارة';
        $this->forPharmacy($order->pharmacy_id, 'order', 'طلب جديد لصيدليتك', "الطلب {$order->order_number} بانتظار المراجعة.", $url);
        $this->forManagers('order', 'طلب جديد في المنصة', "الطلب {$order->order_number} موجّه إلى {$pharmacyName}.", $url);
    }

    public function consultationCreated(Consultation $consultation): void
    {
        $url = route('admin.consultations.show', $consultation);
        if ($consultation->pharmacy_id) {
            $this->forPharmacy($consultation->pharmacy_id, 'consultation', 'استشارة جديدة لصيدليتك', 'وصلت استشارة جديدة مرتبطة بأحد أدويتك.', $url);
        }
        $this->forManagers('consultation', 'استشارة جديدة', 'وصلت استشارة جديدة من الموقع.', $url);
    }

    public function subscriptionUpdated(Pharmacy $pharmacy): void
    {
        $this->forPharmacy($pharmacy->id, 'subscription', 'تحديث اشتراك الصيدلية', "تم تحديث حالة اشتراك {$pharmacy->trans_name} إلى: {$pharmacy->subscription_status}.", route('admin.profile'));
    }

    public function forPharmacy(?int $pharmacyId, string $type, string $title, string $body, ?string $url = null): void
    {
        if (! $pharmacyId) return;
        User::where('pharmacy_id', $pharmacyId)->each(fn (User $user) => $this->create($user, $type, $title, $body, $url));
    }

    public function forManagers(string $type, string $title, string $body, ?string $url = null): void
    {
        User::with('role.permissions')->get()->filter(fn (User $user) => $user->isSuperAdmin())->each(fn (User $user) => $this->create($user, $type, $title, $body, $url));
    }

    public function create(User $user, string $type, string $title, string $body, ?string $url = null): void
    {
        AdminNotification::create(compact('type', 'title', 'body', 'url') + ['user_id' => $user->id]);
    }
}