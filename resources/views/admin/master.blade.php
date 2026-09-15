@php $rtl = app()->getLocale() == 'ar'; @endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $rtl ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'titleT')</title>
    @if ($logo = logo_url())
        <link rel="icon" href="{{ $logo }}">
        <link rel="apple-touch-icon" href="{{ $logo }}">
    @endif

    @yield('css')
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="{{ asset('back/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('back/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('back/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('back/plugins/jqvmap/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('back/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('back/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <link rel="stylesheet" href="{{ asset('back/plugins/daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('back/plugins/summernote/summernote-bs4.min.css') }}">
<style>
        .img-profile {
            object-fit: cover
        }
        /* Logo row at the top of the sidebar. It is kept white with dark text
           because the logo itself is dark: on the green sidebar it disappears. */
        .brand-link {
            display: flex;
            align-items: center;
            gap: .6rem;
            background: #fff;
            border-bottom: 1px solid #e9ecef;
        }
        .brand-link,
        .brand-link:hover,
        .brand-link .brand-text { color: #1f2d3d !important; }
        .brand-link .brand-image {
            float: none;
            margin: 0;
            max-height: 38px;
            width: auto;
            line-height: 1;
            opacity: 1;
        }
        .brand-link .brand-text {
            font-weight: 600;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .colors {
            width: 100px;
            position: fixed;
            right: -60px;
            top: 100px;
            display: flex;
            transition: all .3s ease
        }

        .colors.open {
            right: 0;
        }
        .colors button {
            background: #d9d9d9;
            border: 0;
            width: 40px;
            height: 40px;
        }

        .colors ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            flex-wrap: wrap;
            background: #e4e4e4;
            width: 60px;
            justify-content: center;
            padding: 5px 0;
        }

        .colors ul li {
            width: 20px;
            height: 20px;
            margin: 3px;
            cursor: pointer;
        }
    </style>
        {{-- {{ App::getLocal() }} --}}
    @if ($rtl)
        <style>
            /* ===== Admin RTL baseline ===== */
            body { direction: rtl; text-align: right; }

            /* Sidebar to the right */
            .main-sidebar { right: 0 !important; left: auto !important; }
            .sidebar { padding: 0; }
            .sidebar .nav-item .nav-link { text-align: right; }

            /* Push page content to the left of the right-aligned sidebar */
            .content-wrapper,
            .main-header.navbar,
            .main-footer {
                margin-right: 250px !important;
                margin-left: 0 !important;
            }

            /* Collapsed (mini) sidebar */
            .sidebar-collapse .content-wrapper,
            .sidebar-collapse .main-header.navbar,
            .sidebar-collapse .main-footer {
                margin-right: 4.6rem !important;
                margin-left: 0 !important;
            }
            /* On mobile the sidebar slides off-canvas to the right */
            @media (max-width: 991.98px) {
                .content-wrapper,
                .main-header.navbar,
                .main-footer { margin-right: 0 !important; }
                .main-sidebar {
                    margin-left: 0 !important;
                    transform: translateX(100%);
                    transition: transform .3s ease-in-out;
                }
                .sidebar-open .main-sidebar { transform: translateX(0); }
            }

            /* Footer version block to the left in RTL */
            .main-footer .float-right { float: left !important; }

            /* Sidebar treeview expand/collapse arrow: point it toward the text (left edge) */
            .nav-sidebar .nav-link > p > .right,
            .nav-sidebar .nav-link > .right {
                left: 1rem;
                right: auto;
                position: absolute;
            }
            .nav-sidebar .menu-open > .nav-link > p > .right,
            .nav-sidebar .nav-link[aria-expanded="true"] > p > .right {
                transform: rotate(-90deg);
            }

            /* Icon spacing (overrides the LTR default margin-right) */
            .nav-sidebar .nav-icon { margin-left: .5rem; margin-right: 0; }
            .navbar-nav .nav-icon { margin-left: .5rem; margin-right: 0; }

            /* Navbar left/right link groups */
            .ml-auto, .mx-auto { margin-left: unset !important; margin-right: auto !important; }
            .navbar-nav.ml-auto { margin-right: auto !important; margin-left: 0 !important; }

            /* Right-side badges in menu items */
            .nav-sidebar .badge.right { left: 2.2rem; right: auto; }

            /* Dropdown menus */
            .navbar .dropdown-menu { right: auto; left: 0; }

            /* Sidebar logo row follows the right-hand sidebar */
            .brand-link { flex-direction: row; text-align: right; }

            /* Color-picker widget flips to the left edge */
            .colors { right: unset; left: -60px; }
            .colors.open { right: unset; left: 0; }
        </style>
    @endif
    </head>

<body id="page-top" class="hold-transition sidebar-mini layout-fixed">
    <div class="colors">
        <button><i class="fas fa-cog"></i></button>
        <ul>
            <li class="bg-gradient-primary"></li>
            <li class="bg-gradient-dark"></li>
            <li class="bg-gradient-success"></li>
            <li class="bg-gradient-info"></li>
            <li class="bg-gradient-warning"></li>
            <li class="bg-gradient-danger"></li>
        </ul>
    </div>

    @include('admin.sidebar')

    <script src="{{ asset('back/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('back/plugins/jquery-ui/jquery-ui.min.js') }}"></script>

    <script>
        $.widget.bridge('uibutton', $.ui.button)
    </script>

    <script src="{{ asset('back/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('back/plugins/chart.js/Chart.min.js') }}"></script>
    <script src="{{ asset('back/plugins/sparklines/sparkline.js') }}"></script>
    <script src="{{ asset('back/plugins/jqvmap/jquery.vmap.min.js') }}"></script>
    <script src="{{ asset('back/plugins/jqvmap/maps/jquery.vmap.usa.js') }}"></script>
    <script src="{{ asset('back/plugins/jquery-knob/jquery.knob.min.js') }}"></script>
    <script src="{{ asset('back/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('back/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('back/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
    <script src="{{ asset('back/plugins/summernote/summernote-bs4.min.js') }}"></script>
    <script src="{{ asset('back/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('back/dist/js/adminlte.js') }}"></script>
    <script src="{{ asset('back/dist/js/demo.js') }}"></script>
    <script src="{{ asset('back/dist/js/pages/dashboard.js') }}"></script>

    @yield('scripts')
    <script>
        document.querySelector('.colors button').onclick = () => {
            document.querySelector('.colors').classList.toggle('open')
        }

        document.querySelectorAll('.colors ul li').forEach(el => {
            el.onclick = () => {
                let cl = el.classList[0];
                document.querySelector('#sidebar_color').className = '';
                document.querySelector('#sidebar_color').classList.add(cl)
                localStorage.setItem('cl', cl)
            }
        });

        let oldclass = localStorage.getItem('cl')??'bg-gradient-primary'
        document.querySelector('#sidebar_color').classList.add(oldclass)

    </script>
    <script>
        let userId = '{{ Auth::id() }}'
    </script>
    <script>
    document.getElementById('settingsDropdown').addEventListener('click', function(e) {
        e.preventDefault();
        var menu = document.getElementById('settingsMenu');
        if (menu.style.display === 'none' || menu.style.display === '') {
            menu.style.display = 'block';
        } else {
            menu.style.display = 'none';
        }
    });
</script>
</body>

</html>


