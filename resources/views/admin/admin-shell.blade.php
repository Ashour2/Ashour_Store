<div class="wrapper admin-workspace">
    <header class="admin-topbar">
        <div class="admin-brand">
            @if ($logo = logo_url())
                <img src="{{ $logo }}" alt="{{ tsetting('site_name') }}" width="34" height="34" style="object-fit:contain">
            @else
                <i class="fas fa-clinic-medical text-primary"></i>
            @endif
            <div><strong>{{ tsetting('site_name', 'Line Pharm') }}</strong><small class="d-block">لوحة إدارة المنصة</small></div>
<span class="mr-auto"></span>
            @include('admin.header-actions')
        </div>
        <nav class="admin-toolbar" aria-label="أدوات الإدارة">
            @can('warehouses.manage')<a class="admin-tool" href="{{ route('admin.warehouses.manage') }}"><i class="fas fa-warehouse"></i><span>المستودعات</span></a><a class="admin-tool" href="{{ route('admin.wholesale.orders') }}"><i class="fas fa-truck"></i><span>طلبات التوريد</span></a>@endcan
            <a class="admin-tool {{ request()->routeIs('admin.index') ? 'active' : '' }}" href="{{ route('admin.index') }}"><i class="fas fa-house"></i><span>الرئيسية</span></a>

            @can('categories.view')
            <div class="admin-tool-dropdown dropdown">
                <button class="admin-tool {{ request()->routeIs('admin.categories.*') ? 'active' : '' }} border-0 bg-transparent" data-toggle="dropdown"><i class="fas fa-tags"></i><span>التصنيفات <i class="fas fa-chevron-down admin-chevron"></i></span></button>
                <div class="dropdown-menu admin-dropdown-menu">
                    <a class="dropdown-item" href="{{ route('admin.categories.index') }}"><i class="fas fa-list"></i> كل التصنيفات</a>
                    @can('categories.create')<a class="dropdown-item" href="{{ route('admin.categories.create') }}"><i class="fas fa-plus"></i> إضافة تصنيف</a><a class="dropdown-item" href="{{ route('admin.categories.import.form') }}"><i class="fas fa-file-import"></i> استيراد Excel</a>@endcan
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ route('admin.categories.export.excel') }}"><i class="fas fa-file-excel"></i> تصدير Excel</a>
                    <a class="dropdown-item" href="{{ route('admin.categories.export.pdf') }}"><i class="fas fa-file-pdf"></i> تصدير PDF</a>
                </div>
            </div>
            @endcan

            @can('products.view')
            <div class="admin-tool-dropdown dropdown">
                <button class="admin-tool {{ request()->routeIs('admin.products.*') ? 'active' : '' }} border-0 bg-transparent" data-toggle="dropdown"><i class="fas fa-capsules"></i><span>المنتجات <i class="fas fa-chevron-down admin-chevron"></i></span></button>
                <div class="dropdown-menu admin-dropdown-menu">
                    <a class="dropdown-item" href="{{ route('admin.products.index') }}"><i class="fas fa-list"></i> كل المنتجات</a>
                    @can('products.create')<a class="dropdown-item" href="{{ route('admin.products.create') }}"><i class="fas fa-plus"></i> إضافة منتج</a><a class="dropdown-item" href="{{ route('admin.products.import.form') }}"><i class="fas fa-file-import"></i> استيراد Excel</a>@endcan
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ route('admin.products.export.excel') }}"><i class="fas fa-file-excel"></i> تصدير Excel</a>
                    <a class="dropdown-item" href="{{ route('admin.products.export.pdf') }}"><i class="fas fa-file-pdf"></i> تصدير PDF</a>
                </div>
            </div>
            @endcan

            @can('pharmacies.view')
            <div class="admin-tool-dropdown dropdown">
                <button class="admin-tool {{ request()->routeIs('admin.pharmacies.*') ? 'active' : '' }} border-0 bg-transparent" data-toggle="dropdown"><i class="fas fa-clinic-medical"></i><span>الصيدليات <i class="fas fa-chevron-down admin-chevron"></i></span></button>
                <div class="dropdown-menu admin-dropdown-menu">
                    <a class="dropdown-item" href="{{ route('admin.pharmacies.index') }}"><i class="fas fa-list"></i> كل الصيدليات</a>
                    @can('pharmacies.create')<a class="dropdown-item" href="{{ route('admin.pharmacies.create') }}"><i class="fas fa-plus"></i> إضافة صيدلية</a>@endcan
                    @can('pharmacies.edit')<a class="dropdown-item" href="{{ route('admin.pharmacies.subscriptions') }}"><i class="fas fa-credit-card"></i> إدارة الاشتراكات</a>@endcan
                </div>
            </div>
            @endcan

            @can('articles.view')
            <div class="admin-tool-dropdown dropdown"><button class="admin-tool {{ request()->routeIs('admin.articles.*') ? 'active' : '' }} border-0 bg-transparent" data-toggle="dropdown"><i class="fas fa-newspaper"></i><span>المقالات <i class="fas fa-chevron-down admin-chevron"></i></span></button><div class="dropdown-menu admin-dropdown-menu"><a class="dropdown-item" href="{{ route('admin.articles.index') }}"><i class="fas fa-list"></i> كل المقالات</a>@can('articles.create')<a class="dropdown-item" href="{{ route('admin.articles.create') }}"><i class="fas fa-plus"></i> إضافة مقال</a>@endcan</div></div>
            @endcan
            @can('consultations.view')<a class="admin-tool {{ request()->routeIs('admin.consultations.*') ? 'active' : '' }}" href="{{ route('admin.consultations.index') }}"><i class="fas fa-comment-medical"></i><span>الاستشارات</span></a>@endcan
            @can('orders.view')<a class="admin-tool {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}"><i class="fas fa-receipt"></i><span>الطلبات</span></a>@endcan
            @can('customers.view')<a class="admin-tool {{ request()->routeIs('admin.customers') ? 'active' : '' }}" href="{{ route('admin.customers') }}"><i class="fas fa-users"></i><span>العملاء</span></a>@endcan
            <span class="admin-toolbar-divider"></span>
            <div class="admin-tool-dropdown dropdown"><button class="admin-tool {{ request()->routeIs('admin.settings.*','admin.users.*','admin.roles.*','admin.profile') ? 'active' : '' }} border-0 bg-transparent" data-toggle="dropdown"><i class="fas fa-gear"></i><span>الإعدادات <i class="fas fa-chevron-down admin-chevron"></i></span></button><div class="dropdown-menu admin-dropdown-menu">@can('settings.manage')<a class="dropdown-item" href="{{ route('admin.settings.edit') }}"><i class="fas fa-sliders"></i> إعدادات الموقع</a>@endcan @can('users.view')<a class="dropdown-item" href="{{ route('admin.users.index') }}"><i class="fas fa-users-cog"></i> المستخدمون</a>@endcan @can('roles.view')<a class="dropdown-item" href="{{ route('admin.roles.index') }}"><i class="fas fa-user-shield"></i> الأدوار والصلاحيات</a>@endcan <div class="dropdown-divider"></div><a class="dropdown-item" href="{{ route('admin.profile') }}"><i class="fas fa-user"></i> حسابي</a></div></div>
            <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="admin-tool danger border-0 bg-transparent"><i class="fas fa-right-from-bracket"></i><span>خروج</span></button></form>
        </nav>
    </header>
    <main class="content-wrapper"><div class="content pt-3"><div class="container-fluid">@yield('content')</div></div></main>
    <footer class="main-footer"><strong>{{ tsetting('site_name', 'Line Pharm') }}</strong> · لوحة إدارة المنصة</footer>
</div>
