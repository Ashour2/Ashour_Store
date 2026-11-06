<div class="wrapper">

    <style>
        .quick-actions-header-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            margin: 5px 4px;
            padding: 7px 13px !important;
            border-radius: 999px;
            background: #28a745;
            color: #fff !important;
            font-size: .88rem;
            line-height: 1;
            box-shadow: none;
        }
        .quick-actions-header-button:hover,
        .quick-actions-header-button:focus { background: #218838; color: #fff !important; }
        .quick-actions-header-menu { min-width: 220px; padding: 6px; border: 1px solid #d9e8dc; box-shadow: 0 7px 20px rgba(15,23,42,.16); }
        .quick-actions-header-menu .dropdown-item { padding: 9px 10px; border-radius: 5px; color: #275a37; }
        .quick-actions-header-menu .dropdown-item:hover { background: #eaf7ee; color: #147437; }
        .quick-actions-header-menu .dropdown-item i { width: 20px; }
    </style>

    @if (session('impersonator_id'))
        <div style="position:fixed;z-index:3000;bottom:20px;left:20px;background:#f59e0b;color:#111;padding:8px 12px;border-radius:8px;box-shadow:0 4px 14px rgba(0,0,0,.2)">
            <form method="POST" action="{{ route('admin.impersonation.leave') }}">@csrf <button class="btn btn-sm btn-dark">الرجوع لحساب المدير</button></form>
        </div>
    @endif

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav" @if (LaravelLocalization::getCurrentLocaleDirection() == 'rtl') style="margin-right: 0; margin-left: auto;" @endif>
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                    <i class="fas fa-bars"></i>
                </a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="{{ route('home') }}" class="nav-link" target="_blank" rel="noopener">
                    <i class="fas fa-external-link-alt"></i> {{ __('admin.go_to_site') }}
                </a>
            </li>

            <!-- Language Dropdown Menu -->
            <li class="nav-item dropdown d-none d-sm-inline-block">
                <a class="nav-link dropdown-toggle" href="#" id="languageDropdown" role="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-globe"></i> {{ __('admin.langs') }}
                </a>
                <div class="dropdown-menu" aria-labelledby="languageDropdown">
                    @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                        <a class="dropdown-item" rel="alternate" hreflang="{{ $localeCode }}"
                            href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
                            {{ $properties['native'] }}
                        </a>
                    @endforeach
                </div>
            </li>

            <li class="nav-item dropdown d-none d-sm-inline-block">
                <a class="nav-link quick-actions-header-button dropdown-toggle" href="#" id="quickActionsDropdown" role="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-bolt"></i> ماذا تريد أن تفعل هنا؟
                </a>
                <div class="dropdown-menu quick-actions-header-menu" aria-labelledby="quickActionsDropdown">
                    @can('pharmacies.create')
                        <a class="dropdown-item" href="{{ route('admin.pharmacies.create') }}"><i class="fas fa-clinic-medical"></i> إضافة صيدلية</a>
                    @endcan
                    @if (Auth::user()->isPharmacist())
                            <li class="nav-item">
                                <a href="{{ route('admin.my-pharmacy.inventory') }}" class="nav-link">
                                    <i class="fas fa-clinic-medical nav-icon" style="color: white"></i>
                                    <p style="color: wheat">أدويتي في صيدليتي</p>
                                </a>
                            </li>
                            @endif

                            @can('products.create')
                        <a class="dropdown-item" href="{{ route('admin.products.create') }}"><i class="fas fa-capsules"></i> إضافة دواء</a>
                        <a class="dropdown-item" href="{{ route('admin.products.import.form') }}"><i class="fas fa-file-import"></i> استيراد أدوية</a>
                    @endcan
                    @can('categories.create')
                        <a class="dropdown-item" href="{{ route('admin.categories.create') }}"><i class="fas fa-tags"></i> إضافة تصنيف</a>
                    @endcan
                    @can('articles.create')
                        <a class="dropdown-item" href="{{ route('admin.articles.create') }}"><i class="fas fa-newspaper"></i> إضافة مقال</a>
                    @endcan
                    @can('settings.manage')
                        <a class="dropdown-item" href="{{ route('admin.settings.edit') }}"><i class="fas fa-cog"></i> إعدادات الموقع</a>
                    @endcan
                    @can('orders.view')
                        <a class="dropdown-item" href="{{ route('admin.orders.index') }}"><i class="fas fa-receipt"></i> الطلبات</a>
                    @endcan
                </div>
            </li>
        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto"
            @if (LaravelLocalization::getCurrentLocaleDirection() == 'rtl') style="margin-right: auto; margin-left: 0;" @endif>

            <li class="nav-item">
                <a class="nav-link" href="#" role="button">
                    <h6>مرحباً {{ Auth::user()->name }}! 👋
                        @if (Auth::user()->isPharmacist() && Auth::user()->pharmacy_id)
                            <small class="text-muted">— {{ Auth::user()->pharmacy->trans_name }}</small>
                        @endif
                    </h6>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                    <i class="fas fa-expand-arrows-alt"></i>

                </a>
            </li>

        </ul>
    </nav>

    <!-- Main Sidebar Container -->
    <aside id="sidebar_color" class="main-sidebar sidebar-dark-success elevation-4">

        <!-- Brand: the logo uploaded in the site settings -->
        <a href="{{ route('home') }}" class="brand-link" target="_blank" rel="noopener"
            title="{{ __('admin.go_to_site') }}">
            @if ($logo = logo_url())
                <img src="{{ $logo }}" alt="{{ tsetting('site_name') }}" class="brand-image">
            @else
                <i class="fas fa-mortar-pestle"></i>
            @endif
            <span class="brand-text font-weight-light">{{ tsetting('site_name', 'Line Pharm') }}</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                    data-accordion="false"
                    @if (LaravelLocalization::getCurrentLocaleDirection() == 'rtl') style="padding-right: 0; padding-left: 0;" @endif>

                    <!-- Dashboard -->
                    <li class="nav-item menu-open">
                        <a href="#" class="nav-link active">
                            <i class="nav-icon fas fa-store"
                                @if (LaravelLocalization::getCurrentLocaleDirection() == 'rtl') style="margin-left: 0.5rem; margin-right: 0;" @endif></i>
                            <p>
                                {{ __('admin.Ashour_Store') }}
                                <i class="right fas fa-angle-left"
                                    ></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview"
                            @if (LaravelLocalization::getCurrentLocaleDirection() == 'rtl') style="padding-right: 1rem; padding-left: 0;" @endif>
                            <li class="nav-item">
                                <a href="{{ route('admin.index') }}" class="nav-link active">
                                    <i class="far fa-circle nav-icon fas fa-dashboard" style="color: white;"></i>
                                    <p>{{ __('admin.dash') }} </p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    @can('categories.view')
                    <!-- Categories -->
                    <li class="nav-header"></li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-th-large" style="color: white"
                                @if (LaravelLocalization::getCurrentLocaleDirection() == 'rtl') style="margin-left: 0.5rem; margin-right: 0;" @endif></i>
                            <p style="color: wheat">
                                {{ __('admin.categories') }}
                                <i class="fas fa-angle-left right"
                                    ></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview"
                            @if (LaravelLocalization::getCurrentLocaleDirection() == 'rtl') style="padding-right: 1rem; padding-left: 0;" @endif>
                            <li class="nav-item">
                                <a href="{{ route('admin.categories.index') }}" class="nav-link">
                                    <i class="fas fa-list-ul nav-icon" style="color: white"
                                        @if (LaravelLocalization::getCurrentLocaleDirection() == 'rtl') style="margin-left: 0.5rem; margin-right: 0;" @endif></i>
                                    <p style="color: wheat">{{ __('admin.all_categories') }}</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.categories.create') }}" class="nav-link">
                                    <i class="fas fa-plus-circle nav-icon" style="color: white"
                                        @if (LaravelLocalization::getCurrentLocaleDirection() == 'rtl') style="margin-left: 0.5rem; margin-right: 0;" @endif></i>
                                    <p style="color: wheat">{{ __('admin.add_new') }}</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    @endcan

                    @can('products.view')
                    <!-- Products -->
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-gifts" style="color: white"
                                @if (LaravelLocalization::getCurrentLocaleDirection() == 'rtl') style="margin-left: 0.5rem; margin-right: 0;" @endif></i>
                            <p style="color: wheat">
                                {{ __('admin.products') }}
                                <i class="fas fa-angle-left right"
                                    ></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview"
                            @if (LaravelLocalization::getCurrentLocaleDirection() == 'rtl') style="padding-right: 1rem; padding-left: 0;" @endif>
                            <li class="nav-item">
                                <a href="{{ route('admin.products.index') }}" class="nav-link">
                                    <i class="fas fa-list-ul nav-icon" style="color: white"
                                        @if (LaravelLocalization::getCurrentLocaleDirection() == 'rtl') style="margin-left: 0.5rem; margin-right: 0;" @endif></i>
                                    <p style="color: wheat">{{ __('admin.all_products') }}</p>
                                </a>
                            </li>
                            @if (Auth::user()->isPharmacist())
                            <li class="nav-item">
                                <a href="{{ route('admin.my-pharmacy.inventory') }}" class="nav-link">
                                    <i class="fas fa-clinic-medical nav-icon" style="color: white"></i>
                                    <p style="color: wheat">أدويتي في صيدليتي</p>
                                </a>
                            </li>
                            @endif

                            @can('products.create')
                            <li class="nav-item">
                                <a href="{{ route('admin.products.create') }}" class="nav-link">
                                    <i class="fas fa-plus-circle nav-icon" style="color: white"
                                        @if (LaravelLocalization::getCurrentLocaleDirection() == 'rtl') style="margin-left: 0.5rem; margin-right: 0;" @endif></i>
                                    <p style="color: wheat">{{ __('admin.add_new') }}</p>
                                </a>
                            </li>
                            @endcan
                        </ul>
                    </li>
                    @endcan

                    @can('pharmacies.view')
                    <!-- Pharmacies (stock locations, manager only) -->
                    <li class="nav-item">
                        <a href="{{ route('admin.pharmacies.index') }}" class="nav-link">
                            <i class="nav-icon fas fa-clinic-medical" style="color: white"
                                @if (LaravelLocalization::getCurrentLocaleDirection() == 'rtl') style="margin-left: 0.5rem; margin-right: 0;" @endif></i>
                            <p style="color: wheat">
                                {{ __('admin.pharmacies') }}
                                @php $pharmacies_count = \App\Models\Pharmacy::count(); @endphp
                                @if ($pharmacies_count)
                                    <span class="badge badge-info right">{{ $pharmacies_count }}</span>
                                @endif
                            </p>
                        </a>
                    </li>
                    @endcan

                    @can('pharmacies.edit')
                    <li class="nav-item">
                        <a href="{{ route('admin.pharmacies.subscriptions') }}" class="nav-link">
                            <i class="nav-icon fas fa-credit-card" style="color: white"></i>
                            <p style="color: wheat">إدارة الاشتراكات</p>
                        </a>
                    </li>
                    @endcan

                    @can('articles.view')
                    <!-- Articles -->
                    <li class="nav-item">
                        <a href="{{ route('admin.articles.index') }}" class="nav-link">
                            <i class="nav-icon fas fa-newspaper" style="color: white"
                                @if (LaravelLocalization::getCurrentLocaleDirection() == 'rtl') style="margin-left: 0.5rem; margin-right: 0;" @endif></i>
                            <p style="color: wheat">{{ __('admin.articles') }}</p>
                        </a>
                    </li>
                    @endcan

                    @can('consultations.view')
                    <!-- Consultations -->
                    <li class="nav-item">
                        <a href="{{ route('admin.consultations.index') }}" class="nav-link">
                            <i class="nav-icon fas fa-comment-medical" style="color: white"
                                @if (LaravelLocalization::getCurrentLocaleDirection() == 'rtl') style="margin-left: 0.5rem; margin-right: 0;" @endif></i>
                            <p style="color: wheat">
                                {{ __('admin.consultations') }}
                                @php $new_consultations = \App\Models\Consultation::where('status','new')->count(); @endphp
                                @if ($new_consultations)
                                    <span class="badge badge-danger right">{{ $new_consultations }}</span>
                                @endif
                            </p>
                        </a>
                    </li>
                    @endcan

                    <li class="my-2"></li>

                    @can('orders.view')
                    <!-- Orders -->
                    <li class="nav-item">
                        <a href="{{ route('admin.orders.index') }}" class="nav-link">
                            <i class="nav-icon fas fa-receipt" style="color: white"
                                @if (LaravelLocalization::getCurrentLocaleDirection() == 'rtl') style="margin-left: 0.5rem; margin-right: 0;" @endif></i>
                            <p style="color: wheat">
                                {{ __('admin.orders') }}
                                @php $new_orders = \App\Models\Order::where('status', 'new')->count(); @endphp
                                @if ($new_orders)
                                    <span class="badge badge-danger right">{{ $new_orders }}</span>
                                @endif
                            </p>
                        </a>
                    </li>
                    @endcan

                    @can('customers.view')
                    <!-- Customers -->
                    <li class="nav-item">
                        <a href="{{ route('admin.customers') }}" class="nav-link">
                            <i class="nav-icon fas fa-users" style="color: white"
                                @if (LaravelLocalization::getCurrentLocaleDirection() == 'rtl') style="margin-left: 0.5rem; margin-right: 0;" @endif></i>
                            <p style="color: wheat">
                                {{ __('admin.customers') }}
                            </p>
                        </a>
                    </li>
                    @endcan

                    <li class="my-2"></li>

                    <!-- Settings Dropdown Menu -->
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-cogs" style="color: white"
                                @if (LaravelLocalization::getCurrentLocaleDirection() == 'rtl') style="margin-left: 0.5rem; margin-right: 0;" @endif></i>
                            <p style="color: wheat">
                                {{ __('admin.settings') }}
                                <i class="fas fa-angle-left right"
                                    ></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview"
                            @if (LaravelLocalization::getCurrentLocaleDirection() == 'rtl') style="padding-right: 1rem; padding-left: 0;" @endif>
                            @can('settings.manage')
                            <li class="nav-item">
                                <a href="{{ route('admin.settings.edit') }}" class="nav-link">
                                    <i class="fas fa-sliders-h nav-icon" style="color: white"
                                        @if (LaravelLocalization::getCurrentLocaleDirection() == 'rtl') style="margin-left: 0.5rem; margin-right: 0;" @endif></i>
                                    <p style="color: wheat">{{ __('admin.site_settings') }}</p>
                                </a>
                            </li>
                            @endcan

                            @can('users.view')
                            <li class="nav-item">
                                <a href="{{ route('admin.users.index') }}" class="nav-link">
                                    <i class="fas fa-users-cog nav-icon" style="color: white"
                                        @if (LaravelLocalization::getCurrentLocaleDirection() == 'rtl') style="margin-left: 0.5rem; margin-right: 0;" @endif></i>
                                    <p style="color: wheat">{{ __('admin.users') }}</p>
                                </a>
                            </li>
                            @endcan

                            @can('roles.view')
                            <li class="nav-item">
                                <a href="{{ route('admin.roles.index') }}" class="nav-link">
                                    <i class="fas fa-user-shield nav-icon" style="color: white"
                                        @if (LaravelLocalization::getCurrentLocaleDirection() == 'rtl') style="margin-left: 0.5rem; margin-right: 0;" @endif></i>
                                    <p style="color: wheat">{{ __('admin.roles') }}</p>
                                </a>
                            </li>
                            @endcan
                            <li class="nav-item">
                                <a href="{{ route('admin.profile') }}" class="nav-link">
                                    <i class="fas fa-unlock-alt nav-icon" style="color: white"
                                        @if (LaravelLocalization::getCurrentLocaleDirection() == 'rtl') style="margin-left: 0.5rem; margin-right: 0;" @endif></i>
                                    <p style="color: wheat">{{ __('admin.my_account') }}</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <hr style="margin: 8px 15px; border-color: rgba(255,255,255,0.2);">
                            </li>
                            {{-- <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="fas fa-sign-out-alt nav-icon" style="color: white"
                                        @if (LaravelLocalization::getCurrentLocaleDirection() == 'rtl') style="margin-left: 0.5rem; margin-right: 0;" @endif></i>
                                    <p style="color: wheat">{{ __('admin.logout') }}</p>
                                </a>
                            </li> --}}
                            <li class="nav-item">
                                <form method="POST" action="{{ route('logout') }}"
                                    style="display: inline; width: 100%;">
                                    @csrf
                                    <a href="#" class="nav-link"
                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                        <i class="fas fa-sign-out-alt nav-icon" style="color: white"
                                            @if (LaravelLocalization::getCurrentLocaleDirection() == 'rtl') style="margin-left: 0.5rem; margin-right: 0;" @endif></i>
                                        <p style="color: wheat">{{ __('admin.logout') }}</p>
                                    </a>
                                </form>
                            </li>
                        </ul>
                    </li>



                </ul>
            </nav>
        </div>
    </aside>

    <div class="content-wrapper">
        <div class="content pt-3">
            <div class="container-fluid">
                @yield('content')
            </div>
        </div>
    </div>

    <footer class="main-footer">
        <strong>Copyright &copy; {{ date('Y') }} <a href="{{ route('home') }}">
                {{ tsetting('site_name', 'Line Pharm') }}
            </a>.</strong>
        {{ __('admin.All rights reserved.') }}
        <div class="float-right d-none d-sm-inline-block"
            @if (LaravelLocalization::getCurrentLocaleDirection() == 'rtl') style="float: left !important;" @endif>
            <b>Version</b> 3.1.0-rc
        </div>
    </footer>

    <aside class="control-sidebar control-sidebar-dark">
    </aside>
</div>
