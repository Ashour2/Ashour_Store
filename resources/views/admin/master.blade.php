<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'titleT')</title>

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

    @if (App::getLocale() == 'ar')
        <style>
            .topbar .dropdown .dropdown-menu {
                right: -60%;
            }
            body {
                direction: rtl;
                text-align: right;
            }

            .colors {
                right: unset;
                left: -60px;
            }

            .colors.open {
                right: unset;
                left: 0;
            }

            .sidebar {
                padding: 0
            }

            .sidebar .nav-item .nav-link {
                text-align: right;
            }

            .sidebar .nav-item .nav-link[data-toggle=collapse]::after {
                float: left;
                transform: rotate(180deg)
            }

            .ml-auto, .mx-auto {
                margin-left: unset!important;
                margin-right: auto!important;
            }
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

    {{-- {{ App::getLocal() }} --}}
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
</body>

</html>
