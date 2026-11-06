@extends('admin.master')
@section('title', 'إدارة اشتراكات الصيدليات')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4"><div><h1 class="h3 mb-1">إدارة اشتراكات الصيدليات</h1><p class="text-muted mb-0">راجع الباقات والتجارب المجانية وتجديد كل صيدلية.</p></div><a href="{{ route('admin.pharmacies.index') }}" class="btn btn-outline-secondary"><i class="fas fa-clinic-medical"></i> الصيدليات</a></div>
@if (session('msg'))<div class="alert alert-{{ session('type', 'success') }}">{{ session('msg') }}</div>@endif
<div class="card"><div class="card-body p-0"><div class="table-responsive"><table class="table table-hover mb-0 subscription-table"><thead><tr><th>الصيدلية</th><th>المالك</th><th>الجوال</th><th>الباقة</th><th>الحالة</th><th>المتبقي</th><th>ينتهي</th><th class="text-center">إجراءات</th></tr></thead><tbody>
@forelse($pharmacies as $pharmacy)
@php
    $formId = 'subscription-form-' . $pharmacy->id;
    $owner = $pharmacy->users->first();
    $daysLeft = $pharmacy->subscription_ends_at ? (int) now()->startOfDay()->diffInDays($pharmacy->subscription_ends_at->copy()->startOfDay(), false) : null;
    $statusLabels = ['active' => 'نشط', 'trial' => 'تجريبي', 'suspended' => 'موقوف'];
    $statusClass = ['active' => 'success', 'trial' => 'warning', 'suspended' => 'danger'][$pharmacy->subscription_status] ?? 'secondary';
@endphp
<tr>
    <td><strong>{{ $pharmacy->trans_name }}</strong><small class="d-block text-muted">{{ $pharmacy->address ?: 'لا يوجد عنوان' }}</small></td>
    <td>{{ $owner?->name ?: '—' }}</td>
    <td dir="ltr" class="text-right">{{ $pharmacy->phone ?: '—' }}</td>
    <td><span class="font-weight-bold">{{ $pharmacy->subscription_plan === 'annual' ? 'سنوي — 100$' : 'شهري — 10$' }}</span></td>
    <td><span class="badge badge-{{ $statusClass }}">{{ $statusLabels[$pharmacy->subscription_status] ?? $pharmacy->subscription_status }}</span></td>
    <td>@if(is_null($daysLeft))<span class="text-muted">بدون تاريخ</span>@elseif($daysLeft < 0)<span class="text-danger font-weight-bold">منتهي</span>@elseif($daysLeft === 0)<span class="text-warning font-weight-bold">ينتهي اليوم</span>@else<span class="font-weight-bold text-success">{{ $daysLeft }} يوم</span>@endif</td>
    <td>{{ optional($pharmacy->subscription_ends_at)->format('Y-m-d') ?: '—' }}</td>
    <td class="text-center"><div class="btn-group subscription-actions">
        <form method="POST" action="{{ route('admin.pharmacies.subscriptions.action', $pharmacy) }}">@csrf <input type="hidden" name="action" value="activate"><button class="btn btn-sm btn-success" title="تفعيل"><i class="fas fa-check"></i></button></form>
        <form method="POST" action="{{ route('admin.pharmacies.subscriptions.action', $pharmacy) }}">@csrf <input type="hidden" name="action" value="renew"><button class="btn btn-sm btn-info" title="تجديد"><i class="fas fa-rotate"></i></button></form>
<form method="POST" action="{{ route('admin.pharmacies.subscriptions.action', $pharmacy) }}" onsubmit="return confirm('هل تريد إيقاف الاشتراك؟')">@csrf <input type="hidden" name="action" value="suspend"><button class="btn btn-sm btn-warning" title="إيقاف الاشتراك"><i class="fas fa-pause"></i></button></form>
        <a class="btn btn-sm btn-primary" href="{{ route('admin.pharmacies.edit', $pharmacy) }}" title="تعديل"><i class="fas fa-pen"></i></a>
        @if ($pharmacy->wa_phone)<a class="btn btn-sm btn-success" href="https://wa.me/{{ $pharmacy->wa_phone }}" target="_blank" rel="noopener" title="تواصل واتساب"><i class="fab fa-whatsapp"></i></a>@endif
        <form method="POST" action="{{ route('admin.pharmacies.destroy', $pharmacy) }}" onsubmit="return confirm('هل تريد حذف الصيدلية وإيقاف حساباتها؟')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger" title="حذف"><i class="fas fa-trash"></i></button></form>
    </div></td>
</tr>
@empty<tr><td colspan="8" class="text-center py-4 text-muted">لا توجد صيدليات</td></tr>@endforelse
</tbody></table></div></div></div>
<div class="mt-3">{{ $pharmacies->links() }}</div>
@endsection