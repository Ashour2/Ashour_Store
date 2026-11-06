<div class="wrapper pharmacist-workspace">
    @if (session('impersonator_id'))
        <div class="impersonation-banner" role="alert">
            <span><i class="fas fa-user-secret"></i> أنت الآن في وضع الانتحال — تتصفح النظام بصلاحيات حساب الصيدلي.</span>
            <form method="POST" action="{{ route('admin.impersonation.leave') }}">@csrf<button type="submit"><i class="fas fa-arrow-rotate-right"></i> إنهاء الانتحال والرجوع كمدير</button></form>
        </div>
    @endif
    @php
        $billingPharmacy = auth()->user()->pharmacy;
        $billingEndsAt = $billingPharmacy?->subscription_ends_at;
        $billingDaysLeft = $billingEndsAt ? max(0, (int) now()->startOfDay()->diffInDays($billingEndsAt->copy()->startOfDay(), false)) : null;
        $billingPlan = $billingPharmacy?->subscription_plan === 'annual' ? 'الخطة السنوية' : 'الخطة الشهرية';
    @endphp
    <div class="pharmacy-billing-strip {{ $billingPharmacy?->subscription_status === 'suspended' ? 'billing-suspended' : '' }}">
        <span class="billing-dot"></span>
        @if ($billingPharmacy?->subscription_status === 'trial')
            <span><b>تجربتك المجانية</b> · متبقي {{ $billingDaysLeft }} يوم</span>
        @elseif ($billingPharmacy?->subscription_status === 'suspended')
            <span><b>الاشتراك متوقف</b> · تواصل مع إدارة المنصة للتجديد</span>
        @elseif ($billingEndsAt)
            <span><b>{{ $billingPlan }}</b> · متبقي {{ $billingDaysLeft }} يوم للتجديد</span>
        @else
            <span><b>{{ $billingPlan }}</b> · الاشتراك فعّال</span>
        @endif
        <i class="fas fa-receipt"></i>
    </div>
    <header class="pharmacist-topbar">
        <div class="pharmacist-brand">
            @if ($logo = logo_url())<img src="{{ $logo }}" alt="" width="32" height="32" style="object-fit:contain">@else<i class="fas fa-clinic-medical text-primary"></i>@endif
            <div><strong>{{ auth()->user()->pharmacy->trans_name }}</strong><small class="d-block">مساحة عمل الصيدلي</small></div>
<span class="mr-auto"></span>
            @include('admin.header-actions')
        </div>
        <nav class="pharmacist-toolbar" aria-label="أدوات الصيدلي">
            <a class="pharmacist-tool {{ request()->routeIs('admin.wholesale.catalog', 'admin.wholesale.warehouse') ? 'active' : '' }}" href="{{ route('admin.wholesale.catalog') }}"><i class="fas fa-warehouse"></i><span>المستودعات</span></a>
            <a class="pharmacist-tool {{ request()->routeIs('admin.wholesale.orders*') ? 'active' : '' }}" href="{{ route('admin.wholesale.orders') }}"><i class="fas fa-truck-loading"></i><span>طلبات التوريد</span></a>
            @if (session('impersonator_id'))
                <form method="POST" action="{{ route('admin.impersonation.leave') }}">@csrf<button type="submit" class="pharmacist-tool return-admin border-0"><i class="fas fa-user-shield"></i><span>إنهاء الانتحال</span></button></form>
                <span class="pharmacist-toolbar-divider"></span>
            @endif
            <a class="pharmacist-tool {{ request()->routeIs('admin.index') ? 'active' : '' }}" href="{{ route('admin.index') }}"><i class="fas fa-house"></i><span>الرئيسية</span></a>
            <a class="pharmacist-tool {{ request()->routeIs('admin.my-pharmacy.*') ? 'active' : '' }}" href="{{ route('admin.my-pharmacy.inventory') }}"><i class="fas fa-capsules"></i><span>أدويتي</span></a>
            @can('products.create')<a class="pharmacist-tool" href="{{ route('admin.products.create') }}"><i class="fas fa-circle-plus"></i><span>إضافة دواء</span></a>@endcan
            @can('categories.view')<a class="pharmacist-tool {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}"><i class="fas fa-tags"></i><span>التصنيفات</span></a>@endcan
            @can('orders.view')<a class="pharmacist-tool {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}"><i class="fas fa-receipt"></i><span>الطلبات</span></a>@endcan
            <span class="pharmacist-toolbar-divider"></span>
            <a class="pharmacist-tool" href="{{ route('admin.profile') }}"><i class="fas fa-user"></i><span>حسابي</span></a>
            <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="pharmacist-tool danger border-0 bg-transparent"><i class="fas fa-right-from-bracket"></i><span>خروج</span></button></form>
        </nav>
    </header>
    <main class="content-wrapper"><div class="content pt-3"><div class="container-fluid">@yield('content')</div></div></main>
    <footer class="main-footer"><strong>{{ tsetting('site_name', 'Line Pharm') }}</strong> · مساحة الصيدلي</footer>
</div>
