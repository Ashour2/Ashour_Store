<div class="wrapper admin-workspace">
    <header class="admin-topbar">
        <div class="admin-brand"><i class="fas fa-warehouse text-primary"></i><div><strong>{{ auth()->user()->warehouse?->name ?? 'المستودع' }}</strong><small class="d-block">مساحة المستودع الدوائي</small></div><span class="mr-auto"></span>@include('admin.header-actions')</div>
        <nav class="admin-toolbar" aria-label="أدوات المستودع">
            <a class="admin-tool {{ request()->routeIs('admin.warehouse.*') ? 'active' : '' }}" href="{{ route('admin.warehouse.inventory') }}"><i class="fas fa-boxes"></i><span>الأدوية والأسعار</span></a>
            <a class="admin-tool {{ request()->routeIs('admin.wholesale.orders*') ? 'active' : '' }}" href="{{ route('admin.wholesale.orders') }}"><i class="fas fa-truck"></i><span>طلبات الصيدليات</span></a>
            <a class="admin-tool" href="{{ route('admin.profile') }}"><i class="fas fa-user"></i><span>حسابي</span></a>
            <form method="POST" action="{{ route('logout') }}">@csrf<button class="admin-tool border-0 bg-transparent" type="submit"><i class="fas fa-sign-out-alt"></i><span>خروج</span></button></form>
        </nav>
    </header>
    <main class="content-wrapper"><div class="content pt-3"><div class="container-fluid">@yield('content')</div></div></main>
</div>
