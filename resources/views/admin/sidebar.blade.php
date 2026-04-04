<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light"
        @if(LaravelLocalization::getCurrentLocaleDirection() == 'rtl')
            style="margin-right: 250px; margin-left: 0;"
        @endif
    >
        <!-- Left navbar links -->
        <ul class="navbar-nav"
            @if(LaravelLocalization::getCurrentLocaleDirection() == 'rtl')
                style="margin-right: 0; margin-left: auto;"
            @endif
        >
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                    <i class="fas fa-bars"></i>
                </a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="index3.html" class="nav-link">{{ __('admin.home') }}</a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="#" class="nav-link">{{ __('admin.contact') }}</a>
            </li>

            <!-- Language Dropdown Menu -->
            <li class="nav-item dropdown d-none d-sm-inline-block">
                <a class="nav-link dropdown-toggle" href="#" id="languageDropdown" role="button"
                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-globe"></i> {{ __('admin.langs') }}
                </a>
                <div class="dropdown-menu" aria-labelledby="languageDropdown">
                    @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                        <a class="dropdown-item" rel="alternate" hreflang="{{ $localeCode }}"
                           href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
                            {{ $properties['native'] }}
                        </a>
                    @endforeach
                </div>
            </li>
        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto"
            @if(LaravelLocalization::getCurrentLocaleDirection() == 'rtl')
                style="margin-right: auto; margin-left: 0;"
            @endif
        >
            <li class="nav-item">
                <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                    <i class="fas fa-expand-arrows-alt"></i>
                </a>
            </li>
        </ul>
    </nav>


    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-success elevation-4"
        @if(LaravelLocalization::getCurrentLocaleDirection() == 'rtl')
            style="right: 0; left: auto;"
        @endif
    >

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                    data-accordion="false"
                    @if(LaravelLocalization::getCurrentLocaleDirection() == 'rtl')
                        style="padding-right: 0; padding-left: 0;"
                    @endif
                >

                    <!-- Dashboard -->
                    <li class="nav-item menu-open">
                        <a href="#" class="nav-link active">
                            <i class="nav-icon fas fa-store"
                                @if(LaravelLocalization::getCurrentLocaleDirection() == 'rtl')
                                    style="margin-left: 0.5rem; margin-right: 0;"
                                @endif
                            ></i>
                            <p>
                                {{ __('admin.Ashour_Store') }}
                                <i class="right fas fa-angle-left"
                                    @if(LaravelLocalization::getCurrentLocaleDirection() == 'rtl')
                                        style="transform: rotate(180deg);"
                                    @endif
                                ></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview"
                            @if(LaravelLocalization::getCurrentLocaleDirection() == 'rtl')
                                style="padding-right: 1rem; padding-left: 0;"
                            @endif
                        >
                            <li class="nav-item">
                                <a href="{{ route('admin.index') }}" class="nav-link active">
                                    <i class="far fa-circle nav-icon fas fa-dashboard" style="color: white;"></i>
                                    <p>{{ __('admin.dash') }} </p>
                                </a>
                            </li>
                        </ul>
                    </li>


                    <li class="nav-header"></li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-th-large" style="color: white"
                                @if(LaravelLocalization::getCurrentLocaleDirection() == 'rtl')
                                    style="margin-left: 0.5rem; margin-right: 0;"
                                @endif
                            ></i>
                            <p style="color: wheat">
                                {{ __('admin.categories') }}
                                <i class="fas fa-angle-left right"
                                    @if(LaravelLocalization::getCurrentLocaleDirection() == 'rtl')
                                        style="transform: rotate(180deg);"
                                    @endif
                                ></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview"
                            @if(LaravelLocalization::getCurrentLocaleDirection() == 'rtl')
                                style="padding-right: 1rem; padding-left: 0;"
                            @endif
                        >
                            <li class="nav-item">
                                <a href="{{ route('admin.categories.index') }}" class="nav-link">
                                    <i class="fas fa-list-ul nav-icon" style="color: white"
                                        @if(LaravelLocalization::getCurrentLocaleDirection() == 'rtl')
                                            style="margin-left: 0.5rem; margin-right: 0;"
                                        @endif
                                    ></i>
                                    <p style="color: wheat">{{ __('admin.all_categories') }}</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.categories.create') }}" class="nav-link">
                                    <i class="fas fa-plus-circle nav-icon" style="color: white"
                                        @if(LaravelLocalization::getCurrentLocaleDirection() == 'rtl')
                                            style="margin-left: 0.5rem; margin-right: 0;"
                                        @endif
                                    ></i>
                                    <p style="color: wheat">{{ __('admin.add_new') }}</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-gifts" style="color: white"
                                @if(LaravelLocalization::getCurrentLocaleDirection() == 'rtl')
                                    style="margin-left: 0.5rem; margin-right: 0;"
                                @endif
                            ></i>
                            <p style="color: wheat">
                                {{ __('admin.products') }}
                                <i class="fas fa-angle-left right"
                                    @if(LaravelLocalization::getCurrentLocaleDirection() == 'rtl')
                                        style="transform: rotate(180deg);"
                                    @endif
                                ></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview"
                            @if(LaravelLocalization::getCurrentLocaleDirection() == 'rtl')
                                style="padding-right: 1rem; padding-left: 0;"
                            @endif
                        >
                            <li class="nav-item">
                                <a href="{{ route('admin.products.index') }}" class="nav-link">
                                    <i class="fas fa-list-ul nav-icon" style="color: white"
                                        @if(LaravelLocalization::getCurrentLocaleDirection() == 'rtl')
                                            style="margin-left: 0.5rem; margin-right: 0;"
                                        @endif
                                    ></i>
                                    <p style="color: wheat">{{ __('admin.all_products') }}</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.products.create') }}" class="nav-link">
                                    <i class="fas fa-plus-circle nav-icon" style="color: white"
                                        @if(LaravelLocalization::getCurrentLocaleDirection() == 'rtl')
                                            style="margin-left: 0.5rem; margin-right: 0;"
                                        @endif
                                    ></i>
                                    <p style="color: wheat">{{ __('admin.add_new') }}</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="my-2"></li>

                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-receipt" style="color: white"
                                @if(LaravelLocalization::getCurrentLocaleDirection() == 'rtl')
                                    style="margin-left: 0.5rem; margin-right: 0;"
                                @endif
                            ></i>
                            <p style="color: wheat">
                                {{ __('admin.orders') }}
                            </p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fab fa-cc-amazon-pay" style="color: white"
                                @if(LaravelLocalization::getCurrentLocaleDirection() == 'rtl')
                                    style="margin-left: 0.5rem; margin-right: 0;"
                                @endif
                            ></i>
                            <p style="color: wheat">
                                {{ __('admin.payments') }}
                            </p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-users" style="color: white"
                                @if(LaravelLocalization::getCurrentLocaleDirection() == 'rtl')
                                    style="margin-left: 0.5rem; margin-right: 0;"
                                @endif
                            ></i>
                            <p style="color: wheat">
                                {{ __('admin.customers') }}
                            </p>
                        </a>
                    </li>

                    <li class="my-2"></li>

                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-hand-sparkles" style="color: white"
                                @if(LaravelLocalization::getCurrentLocaleDirection() == 'rtl')
                                    style="margin-left: 0.5rem; margin-right: 0;"
                                @endif
                            ></i>
                            <p style="color: wheat">
                                {{ __('admin.role') }}
                                <i class="fas fa-angle-left right"
                                    @if(LaravelLocalization::getCurrentLocaleDirection() == 'rtl')
                                        style="transform: rotate(180deg);"
                                    @endif
                                ></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview"
                            @if(LaravelLocalization::getCurrentLocaleDirection() == 'rtl')
                                style="padding-right: 1rem; padding-left: 0;"
                            @endif
                        >
                            <li class="nav-item">
                                <a href="{{ 'normalUsers.index' }}" class="nav-link">
                                    <i class="fas fa-list-ul nav-icon" style="color: white"
                                        @if(LaravelLocalization::getCurrentLocaleDirection() == 'rtl')
                                            style="margin-left: 0.5rem; margin-right: 0;"
                                        @endif
                                    ></i>
                                    <p style="color: wheat">{{ __('admin.all_roles') }}</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ 'normalUsers.create' }}" class="nav-link">
                                    <i class="fas fa-plus-circle nav-icon" style="color: white"
                                        @if(LaravelLocalization::getCurrentLocaleDirection() == 'rtl')
                                            style="margin-left: 0.5rem; margin-right: 0;"
                                        @endif
                                    ></i>
                                    <p style="color: wheat">{{ __('admin.add_new') }}</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                </ul>
            </nav>
        </div>
    </aside>

    <div class="content-wrapper"
        @if(LaravelLocalization::getCurrentLocaleDirection() == 'rtl')
            style="margin-right: 250px; margin-left: 0;"
        @else
            style="margin-left: 250px; margin-right: 0;"
        @endif
    >
        @yield('content')
    </div>

    <footer class="main-footer"
        @if(LaravelLocalization::getCurrentLocaleDirection() == 'rtl')
            style="margin-right: 250px; margin-left: 0; text-align: right;"
        @else
            style="margin-left: 250px; margin-right: 0; text-align: left;"
        @endif
    >
        <strong>Copyright &copy; 2014-2020 <a href="https://adminlte.io">
                ashour store
            </a>.</strong>
        {{ __('admin.All rights reserved.') }}
        <div class="float-right d-none d-sm-inline-block"
            @if(LaravelLocalization::getCurrentLocaleDirection() == 'rtl')
                style="float: left !important;"
            @endif
        >
            <b>Version</b> 3.1.0-rc
        </div>
    </footer>

    <aside class="control-sidebar control-sidebar-dark">
    </aside>
</div>
