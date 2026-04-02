<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
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
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                    <i class="fas fa-expand-arrows-alt"></i>
                </a>
            </li>
        </ul>
    </nav>


    <!-- Main Sidebar Container -->
    <adside class="main-sidebar sidebar-dark-success elevation-4">


        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                    data-accordion="false">

                    <!-- Dashboard -->
                    <li class="nav-item menu-open">
                        <a href="#" class="nav-link active">
                            <i class="nav-icon fas fa-store"></i>
                            <p>
                                {{ __('admin.Ashour_Store') }}
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ 'mainPage' }}" class="nav-link active">
                                    <i class="far fa-circle nav-icon fas fa-dashboard" style="color: white;"></i>
                                    <p>{{ __('admin.dash') }} </p>
                                </a>
                            </li>
                        </ul>
                    </li>


                    <li class="nav-header"></li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-th-large" style="color: white"></i>
                            <p style="color: wheat">
                                {{ __('admin.categories') }}
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                {{-- {{ route('admin.categories.index') }} --}}
                                <a href="" class="nav-link">
                                    <i class="fas fa-list-ul nav-icon" style="color: white"></i>
                                    <p style="color: wheat">{{ __('admin.all_categories') }}</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                {{-- {{ route('admin.categories.create') }} --}}
                                <a href="" class="nav-link">
                                    <i class="fas fa-plus-circle nav-icon" style="color: white"></i>
                                    <p style="color: wheat">
                                        {{ __('admin.add_new') }}</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-gifts" style="color: white"></i>
                            <p style="color: wheat">
                                {{ __('admin.products') }}
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ 'normalUsers.index' }}" class="nav-link">
                                    <i class="fas fa-list-ul nav-icon" style="color: white"></i>
                                    <p style="color: wheat">{{ __('admin.all_products') }}</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ 'normalUsers.create' }}" class="nav-link">
                                    <i class="fas fa-plus-circle nav-icon" style="color: white"></i>
                                    <p style="color: wheat">{{ __('admin.add_new') }}</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="my-2"></li>

                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-receipt" style="color: white"></i>
                            <p style="color: wheat">
                                {{ __('admin.orders') }}
                            </p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="#" class="nav-link">

                            <i class="fab fa-cc-amazon-pay" style="color: white"></i>
                            <p style="color: wheat">
                                {{ __('admin.payments') }}
                            </p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-users" style="color: white"></i>
                            <p style="color: wheat">
                                {{ __('admin.customers') }}
                            </p>
                        </a>
                    </li>

                    <li class="my-2"></li>

                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-hand-sparkles" style="color: white"></i>
                            <p style="color: wheat">
                                {{ __('admin.role') }}
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ 'normalUsers.index' }}" class="nav-link">
                                    <i class="fas fa-list-ul nav-icon" style="color: white"></i>
                                    <p style="color: wheat">{{ __('admin.all_roles') }}</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ 'normalUsers.create' }}" class="nav-link">
                                    <i class="fas fa-plus-circle nav-icon" style="color: white"></i>
                                    <p style="color: wheat">{{ __('admin.add_new') }}</p>
                                </a>
                            </li>
                        </ul>
                    </li>


                    {{-- <!-- الإعدادات -->
                        <li class="nav-header">الإعدادات</li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-eye" style="color: white"></i>
                                <p style="color: wheat">الصفحة الشخصية</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-user-edit" style="color: white"></i>
                                <p style="color: wheat">تعديل الملف الشخصي</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ 'changePassword' }}" class="nav-link">
                                <i class="nav-icon fas fa-unlock-alt" style="color: white"></i>
                                <p style="color: wheat">تغيير كلمة المرور</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ 'view.logout' }}" class="nav-link">
                                <i class="nav-icon fas fa-sign-out-alt" style="color: white"></i>
                                <p style="color: wheat">تسجيل الخروج</p>
                            </a>
                        </li> --}}

                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </adside>

    <div class="content-wrapper">
        @yield('content')
    </div>


    <footer class="main-footer">
        <strong>Copyright &copy; 2014-2020 <a href="https://adminlte.io">
                gaza store
            </a>.</strong>
        All rights reserved.
        <div class="float-right d-none d-sm-inline-block">
            <b>Version</b> 3.1.0-rc
        </div>
    </footer>

    <aside class="control-sidebar control-sidebar-dark">
    </aside>
</div>
<!-- ./wrapper -->
