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
    {{-- Matches the Font Awesome Free version used by the public website and category icon codes. --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
            width: auto;
            position: fixed;
            right: 24px;
            bottom: 24px;
            top: auto;
            z-index: 2000;
            display: block;
        }

        .colors button {
            background: #fff;
            color: #0f766e;
            border: 0;
            width: 52px;
            height: 52px;
            box-shadow: 0 3px 12px rgba(15, 23, 42, .22);
            border-radius: 50%;
            cursor: pointer;
        }

        .colors ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            flex-wrap: wrap;
            background: #e4e4e4;
            width: 120px;
            justify-content: center;
            padding: 8px;
            position: absolute;
            bottom: 62px;
            right: 0;
            border-radius: 12px;
            box-shadow: 0 8px 22px rgba(15, 23, 42, .18);
            display: none;
        }

        .colors.open ul { display: flex; }

        .colors ul li {
            width: 20px;
            height: 20px;
            margin: 3px;
            cursor: pointer;
        }
        .sidebar-custom-color { width: 100%; margin-top: 6px; border: 0; height: 28px; cursor: pointer; background: transparent; }
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
            .colors { right: unset; left: 24px; }
            .colors ul { right: unset; left: 0; }
        </style>
    @endif
    <style>
:root{--dash-bg:#f5f7fb;--dash-surface:#fff;--dash-text:#172033;--dash-muted:#697386;--dash-line:#e8edf5;--dash-primary:#2563eb;--dash-sidebar:#101828;}
body{background:var(--dash-bg)!important;color:var(--dash-text);font-family:"Source Sans Pro",Tahoma,Arial,sans-serif}
.content-wrapper{background:var(--dash-bg)!important;min-height:calc(100vh - 57px)!important}.content{padding:24px 12px!important}.container-fluid{max-width:1440px;margin:auto}
.main-header.navbar{min-height:58px;background:#fff!important;border-bottom:1px solid var(--dash-line);box-shadow:0 1px 2px rgba(16,24,40,.04)}
.main-sidebar{background:var(--dash-sidebar)!important;box-shadow:none!important}.brand-link{height:58px;background:var(--dash-sidebar)!important;border-bottom:1px solid rgba(255,255,255,.08)!important}.brand-link,.brand-link:hover,.brand-link .brand-text{color:#fff!important}.brand-link .brand-image{background:#fff;padding:3px;border-radius:7px;max-height:35px}
.nav-sidebar{padding:12px 10px}.nav-sidebar .nav-link{border-radius:8px!important;margin:3px 0;color:#c9d3e3!important;padding:10px 12px!important}.nav-sidebar .nav-link p,.nav-sidebar .nav-link i{color:inherit!important}.nav-sidebar .nav-link:hover{background:rgba(255,255,255,.08)!important;color:#fff!important}.nav-sidebar>.nav-item>.nav-link.active,.nav-sidebar .menu-open>.nav-link{background:#246bfe!important;color:#fff!important;box-shadow:none!important}.nav-sidebar .nav-treeview{padding-right:12px!important}.nav-sidebar .nav-header{height:12px}.nav-sidebar .badge.right{background:#f04438!important;color:#fff!important}
.card,.dashboard-panel{border:1px solid var(--dash-line)!important;border-radius:12px!important;box-shadow:0 1px 2px rgba(16,24,40,.03)!important}.card-header{background:#fff!important;border-bottom:1px solid var(--dash-line)!important}.table{background:#fff}.table thead th,.table .bg-dark{background:#f8fafc!important;color:#475467!important;border-color:var(--dash-line)!important}.table td,.table th{border-color:var(--dash-line)!important;vertical-align:middle!important}.btn{border-radius:7px;font-weight:600}.form-control,.custom-select{border-color:#d0d5dd;border-radius:7px;min-height:38px}
.dashboard-pro{padding-bottom:28px}.dashboard-hero{display:flex;justify-content:space-between;gap:18px;align-items:center;margin:0 0 22px}.dashboard-kicker{color:var(--dash-primary);font-size:.78rem;font-weight:700;margin:0 0 4px}.dashboard-hero h1{font-size:1.65rem;font-weight:750;letter-spacing:-.03em;margin:0 0 6px}.dashboard-hero p{color:var(--dash-muted);margin:0}.dashboard-hero-actions{display:flex;gap:9px;flex-wrap:wrap}.dashboard-pharmacy-note{background:#e8f7ee;color:#17663a;padding:12px 14px;border:1px solid #c3ead1;border-radius:9px;margin-bottom:18px}.stat-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(175px,1fr));gap:14px;margin-bottom:20px}.stat-card{position:relative;display:flex;gap:12px;align-items:center;padding:16px;background:#fff;border:1px solid var(--dash-line);border-radius:12px;color:var(--dash-text)!important;text-decoration:none!important;transition:.18s}.stat-card:hover{transform:translateY(-2px);box-shadow:0 8px 18px rgba(16,24,40,.08)}.stat-icon{display:grid;place-items:center;width:44px;height:44px;border-radius:10px;font-size:1.1rem}.stat-card strong{display:block;font-size:1.45rem;line-height:1.1}.stat-card small{display:block;color:var(--dash-muted);margin-top:4px}.stat-arrow{margin-right:auto;color:#98a2b3;font-size:.75rem}.stat-blue .stat-icon{background:#eaf1ff;color:#2563eb}.stat-purple .stat-icon{background:#f3edff;color:#7c3aed}.stat-orange .stat-icon{background:#fff2e0;color:#d97706}.stat-green .stat-icon{background:#e7f8ee;color:#16a34a}.stat-red .stat-icon{background:#ffeaec;color:#e11d48}.stat-slate .stat-icon{background:#eef2f6;color:#475467}.dashboard-columns{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}.dashboard-panel{background:#fff;overflow:hidden}.dashboard-panel-wide{grid-column:1/-1}.dashboard-panel header{display:flex;align-items:start;justify-content:space-between;gap:10px;padding:16px 18px;border-bottom:1px solid var(--dash-line)}.dashboard-panel h2{font-size:1rem;font-weight:700;margin:0}.dashboard-panel header p{font-size:.8rem;color:var(--dash-muted);margin:3px 0 0}.dashboard-panel header a{color:var(--dash-primary);font-size:.82rem;font-weight:700;text-decoration:none}.dashboard-table-wrap{overflow:auto}.dashboard-table{width:100%;margin:0;border-collapse:collapse}.dashboard-table th{font-size:.78rem;color:var(--dash-muted);font-weight:700;background:#fbfcfe;padding:11px 16px;white-space:nowrap}.dashboard-table td{padding:13px 16px;border-top:1px solid var(--dash-line);white-space:nowrap}.dashboard-table tr:hover td{background:#fbfdff}.dashboard-list>a{display:flex;align-items:center;gap:11px;padding:12px 16px;border-bottom:1px solid var(--dash-line);text-decoration:none;color:var(--dash-text)}.dashboard-list>a:last-child{border-bottom:0}.dashboard-list>a:hover{background:#fbfdff}.dashboard-list img,.list-avatar{width:38px;height:38px;border-radius:9px;object-fit:cover;background:#eef2ff;display:grid;place-items:center;color:#4f46e5}.dashboard-list span:nth-child(2){display:grid;gap:2px;flex:1;min-width:0}.dashboard-list strong{font-size:.88rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.dashboard-list small{color:var(--dash-muted);font-size:.75rem}.status-pill{font-size:.72rem;font-weight:700;padding:4px 8px;border-radius:999px;white-space:nowrap}.status-warning{color:#92400e;background:#fef3c7}.status-info{color:#1d4ed8;background:#dbeafe}.status-success{color:#166534;background:#dcfce7}.status-danger{color:#b42318;background:#fee4e2}.dashboard-empty{text-align:center;color:var(--dash-muted);padding:26px!important;margin:0}
@media(max-width:991.98px){.main-sidebar{box-shadow:0 8px 30px rgba(0,0,0,.3)!important}.content{padding:18px 8px!important}.dashboard-hero{align-items:flex-start;flex-direction:column}.dashboard-columns{grid-template-columns:1fr}.dashboard-panel-wide{grid-column:auto}.main-header .navbar-nav:first-child .nav-item.d-none{display:none!important}}
@media(max-width:575.98px){.dashboard-hero h1{font-size:1.35rem}.stat-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:9px}.stat-card{padding:12px;gap:8px}.stat-icon{width:36px;height:36px}.stat-card strong{font-size:1.15rem}.stat-card small{font-size:.72rem}.stat-arrow{display:none}.dashboard-hero-actions{width:100%}.dashboard-hero-actions .btn{flex:1}.main-header .navbar-nav.ml-auto h6{font-size:.78rem}.main-header .navbar-nav.ml-auto .nav-item:first-child{max-width:140px;overflow:hidden}.quick-actions-header-button{font-size:.72rem!important;padding:6px 9px!important}}
</style>
<style>
.pharmacist-layout .main-header,.pharmacist-layout .content-wrapper,.pharmacist-layout .main-footer{margin-right:0!important;margin-left:0!important}.pharmacist-layout .content-wrapper{min-height:calc(100vh - 132px)!important}.pharmacy-billing-strip{height:30px;display:flex;align-items:center;justify-content:center;gap:7px;background:#f0fdf4;color:#166534;border-bottom:1px solid #bbf7d0;font-size:.72rem}.pharmacy-billing-strip b{font-weight:800}.pharmacy-billing-strip i{color:#159b73}.billing-dot{width:6px;height:6px;border-radius:50%;background:#22c55e}.billing-suspended{background:#fff1f2;color:#b42318;border-color:#fecdd3}.billing-suspended .billing-dot{background:#e11d48}.billing-suspended i{color:#e11d48}.pharmacist-topbar{background:#fff;border-bottom:1px solid #e8edf5;box-shadow:0 1px 2px rgba(16,24,40,.04)}.pharmacist-brand{display:flex;align-items:center;gap:10px;padding:12px 22px;padding-right:92px;border-bottom:1px solid #edf0f5;direction:rtl;position:relative;min-height:68px}.pharmacist-brand>img,.pharmacist-brand>i{position:absolute;right:16px;top:9px;order:-2;flex:0 0 auto}.pharmacist-brand>img{width:50px!important;height:50px!important;object-fit:contain}.pharmacist-brand>i{font-size:1.8rem}.pharmacist-brand strong{font-size:1rem}.pharmacist-brand small{color:#697386}.pharmacist-toolbar{display:flex;align-items:stretch;gap:4px;overflow-x:auto;padding:8px 18px;scrollbar-width:thin}.pharmacist-tool{min-width:75px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:5px;padding:7px 9px;border-radius:8px;color:#475467!important;text-decoration:none!important;font-size:.75rem;white-space:nowrap}.pharmacist-tool i{font-size:1.05rem;color:#2563eb}.pharmacist-tool:hover,.pharmacist-tool.active{background:#eef4ff;color:#1d4ed8!important}.pharmacist-tool.danger i{color:#d92d20}.pharmacist-tool.danger:hover{background:#fff0ef;color:#b42318!important}.pharmacist-toolbar-divider{width:1px;background:#e4e7ec;margin:5px 4px;flex:0 0 1px}@media(max-width:575.98px){.pharmacy-billing-strip{height:auto;min-height:28px;padding:5px 10px;font-size:.64rem}.impersonation-banner{padding:8px 12px;font-size:.75rem;align-items:flex-start}.impersonation-banner button{font-size:.68rem;padding:5px 7px}.pharmacist-brand{padding:10px 13px}.pharmacist-toolbar{padding:7px 9px}.pharmacist-tool{min-width:64px;font-size:.68rem}.pharmacist-layout .content{padding-top:14px!important}}
</style>
<style>
/* لوحـة المدير: شريط أدوات علوي بدلاً من الشريط الجانبي */
.admin-layout .main-header,.admin-layout .content-wrapper,.admin-layout .main-footer{margin-right:0!important;margin-left:0!important}.admin-layout .content-wrapper{min-height:calc(100vh - 142px)!important}.admin-topbar{background:#fff;border-bottom:1px solid #e8edf5;box-shadow:0 1px 2px rgba(16,24,40,.04);position:relative;z-index:20}.admin-brand{display:flex;align-items:center;gap:10px;padding:12px 22px;padding-right:102px;border-bottom:1px solid #edf0f5;direction:rtl;position:relative;min-height:76px}.admin-brand>img,.admin-brand>i{position:absolute;right:16px;top:9px;order:-2;flex:0 0 auto}.admin-brand>img{width:58px!important;height:58px!important;object-fit:contain}.admin-brand>i{font-size:2rem}.admin-brand strong{font-size:1rem}.admin-brand small{color:#697386}.header-actions{display:flex;align-items:center;gap:5px}.header-actions .dropdown{position:relative}.header-action-button,.header-notification-button{height:34px;display:inline-flex;align-items:center;gap:6px;border-radius:7px;padding:5px 8px;color:#475467!important;text-decoration:none!important;font-size:.78rem;white-space:nowrap}.header-action-button:hover{background:#f2f4f7;color:#1d4ed8!important}.header-action-button>i{color:#2563eb}.header-chevron{font-size:.55rem!important;color:#98a2b3!important}.header-notification-button{border:1px solid #efd59a!important;background:#fffaf0!important;color:#9a6700!important;cursor:pointer}.header-notification-button:hover{background:#fff2cc!important}.header-notification-button .notification-icon-wrap>i{font-size:1.08rem;color:#b7791f}.header-actions .dropdown-menu{position:absolute!important;top:calc(100% + 6px)!important;left:0!important;right:auto!important;z-index:1060!important}.header-dropdown-menu{min-width:135px;border:1px solid #e4e7ec;border-radius:9px;box-shadow:0 10px 24px rgba(16,24,40,.12);padding:5px}.header-dropdown-menu .dropdown-item{border-radius:6px;padding:7px 9px;font-size:.8rem}.header-welcome{font-size:.8rem;font-weight:700;color:#344054;white-space:nowrap;padding:0 5px}.header-welcome i{color:#f59e0b}.admin-toolbar{display:flex;align-items:stretch;gap:4px;overflow-x:auto;padding:8px 18px;scrollbar-width:thin;white-space:nowrap}.admin-tool{min-width:76px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:5px;padding:7px 10px;border-radius:8px;color:#475467!important;text-decoration:none!important;font-size:.74rem;white-space:nowrap;line-height:1.15}.admin-tool i{font-size:1.05rem;color:#2563eb}.admin-tool .admin-chevron{font-size:.58rem;margin-right:3px;color:inherit}.admin-tool-dropdown{position:relative;display:flex;flex:0 0 auto}.admin-toolbar .dropdown-menu{position:absolute!important;top:calc(100% + 5px)!important;right:0!important;left:auto!important;z-index:1055!important;float:none!important;transform:none!important}.admin-toolbar .dropdown-menu:not(.show){display:none!important}.admin-toolbar .dropdown-menu.show{display:block!important}.admin-dropdown-menu{border:1px solid #e4e7ec;border-radius:10px;box-shadow:0 10px 24px rgba(16,24,40,.12);padding:6px;min-width:190px;text-align:right}.admin-dropdown-menu .dropdown-item{border-radius:6px;padding:8px 10px;color:#344054;font-size:.82rem}.admin-dropdown-menu .dropdown-item i{width:20px;color:#2563eb}.admin-dropdown-menu .dropdown-item:hover{background:#f1f5ff;color:#1d4ed8}.notification-icon-wrap{position:relative;display:inline-flex}.notification-icon-wrap b{position:absolute;top:-8px;left:-12px;min-width:16px;height:16px;padding:1px 4px;border-radius:999px;background:#d92d20;color:#fff;font-size:.58rem;line-height:14px;font-family:Arial}.notification-dropdown{min-width:330px!important;max-height:440px;overflow-y:auto}.notification-dropdown-head{display:flex;justify-content:space-between;align-items:center;padding:7px 8px 9px}.notification-dropdown-head span{font-size:.7rem;background:#eef4ff;color:#1d4ed8;border-radius:999px;padding:3px 7px}.notification-item{display:flex!important;gap:7px;align-items:flex-start;white-space:normal}.notification-item>i{padding-top:3px}.notification-item span{display:grid;gap:3px}.notification-item small{color:#667085;font-size:.7rem;line-height:1.3}.notification-item.unread{background:#f4f8ff}.notification-empty{padding:20px 10px;text-align:center;color:#98a2b3;font-size:.8rem}.notification-dropdown-actions{display:flex;align-items:center;justify-content:space-between;gap:7px;padding:3px 5px}.notification-dropdown-actions a,.notification-dropdown-actions button{border:0;background:transparent;color:#2563eb;font-size:.74rem;font-weight:700}.notification-page-unread{border-right:3px solid #2563eb!important;background:#f6f9ff!important}.admin-dropdown-menu .dropdown-divider{margin:5px 0}.admin-tool:hover,.admin-tool.active{background:#eef4ff;color:#1d4ed8!important}.admin-toolbar-divider{width:1px;background:#e4e7ec;margin:5px 4px;flex:0 0 1px}.admin-tool.danger i{color:#d92d20}.admin-tool.danger:hover{background:#fff0ef;color:#b42318!important}.impersonation-banner{min-height:48px;background:linear-gradient(90deg,#b42318,#d92d20);color:#fff;display:flex;align-items:center;justify-content:space-between;gap:16px;padding:8px 22px;font-size:.88rem;font-weight:700;box-shadow:0 2px 8px rgba(180,35,24,.18)}.impersonation-banner span i{margin-left:7px}.impersonation-banner button{border:1px solid rgba(255,255,255,.65);border-radius:7px;background:#fff;color:#b42318;padding:6px 11px;font-weight:700;font-size:.78rem;cursor:pointer}.impersonation-banner button:hover{background:#fff1f0}.return-admin{background:#ecfdf3!important;color:#067647!important;border:1px solid #abefc6!important}.return-admin i{color:#067647!important}.return-admin:hover{background:#d1fadf!important;color:#05603a!important}@media(min-width:768px){.admin-toolbar{overflow:visible}}
@media(max-width:767.98px){.admin-topbar{position:sticky;top:0}.admin-brand{padding:9px 12px}.admin-brand small{display:none!important}.admin-brand .btn{font-size:.72rem;padding:4px 7px}.header-actions{gap:2px}.header-action-button span{display:none}.header-action-button{padding:5px 6px}.header-welcome{font-size:.7rem;max-width:105px;overflow:hidden;text-overflow:ellipsis}.header-notification-button{padding:5px 7px}.header-actions .dropdown-menu.show{position:fixed!important;right:12px!important;left:12px!important;top:auto!important;bottom:12px!important;width:auto!important;max-height:62vh;overflow-y:auto}.admin-toolbar{overflow-x:auto;overflow-y:hidden;padding:7px 9px 10px;-webkit-overflow-scrolling:touch;scrollbar-width:thin}.admin-tool,.admin-tool-dropdown{flex:0 0 auto}.admin-toolbar .dropdown-menu.show{position:fixed!important;right:12px!important;left:12px!important;top:auto!important;bottom:12px!important;width:auto!important;max-height:62vh;overflow-y:auto;z-index:1060!important;border-radius:14px;box-shadow:0 16px 42px rgba(16,24,40,.26);padding:8px}.notification-dropdown{min-width:0!important}.admin-dropdown-menu .dropdown-item{padding:11px 12px;font-size:.9rem}.admin-dropdown-menu .dropdown-item i{width:24px}.admin-toolbar .dropdown-menu.show:before{content:"اختر الإجراء";display:block;color:#667085;font-size:.75rem;font-weight:700;padding:5px 8px 8px}.admin-layout .content-wrapper{min-height:calc(100vh - 128px)!important}}
@media(max-width:575.98px){.admin-brand{padding:10px 13px;padding-right:62px}.admin-brand>img,.admin-brand>i{right:12px;top:10px}.admin-brand>img{width:42px!important;height:42px!important}.pharmacist-brand{padding-right:58px}.pharmacist-brand>img,.pharmacist-brand>i{right:12px;top:9px}.pharmacist-brand>img{width:40px!important;height:40px!important}.admin-toolbar{padding:7px 9px}.admin-tool{min-width:65px;font-size:.67rem}.admin-layout .content{padding-top:14px!important}}
.subscription-table th{white-space:nowrap;font-size:.78rem}.subscription-table td{min-width:105px}.subscription-table td:first-child{min-width:190px}.subscription-table select,.subscription-table input{min-width:110px}.subscription-actions form{display:inline-block;margin:0}.subscription-actions .btn{min-width:31px}</style></head>

<body id="page-top" class="hold-transition {{ auth()->check() && auth()->user()->isPharmacist() ? 'pharmacist-layout' : 'admin-layout' }}">@if (auth()->check() && auth()->user()->isPharmacist())
        @include('admin.pharmacist-shell')
    @elseif (auth()->check() && auth()->user()->isWarehouse())
        @include('admin.warehouses.shell')
    @else
        @include('admin.admin-shell')
    @endif

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

    @yield('scripts')
    @yield('js')
    <script>
        const colorsWidget = document.querySelector('.colors');
        const sidebar = document.querySelector('#sidebar_color');
        if (colorsWidget && sidebar) {
            colorsWidget.querySelector('button').onclick = () => colorsWidget.classList.toggle('open');
            colorsWidget.querySelectorAll('ul li[class^="bg-gradient-"]').forEach(el => el.onclick = () => {
                const cl = el.classList[0];
                sidebar.classList.remove('bg-gradient-primary', 'bg-gradient-dark', 'bg-gradient-success', 'bg-gradient-info', 'bg-gradient-warning', 'bg-gradient-danger');
                sidebar.style.backgroundColor = ''; sidebar.classList.add(cl); localStorage.setItem('cl', cl); localStorage.removeItem('sidebarCustomColor');
            });
            const customColorInput = document.getElementById('sidebarCustomColor');
            const customColor = localStorage.getItem('sidebarCustomColor');
            if (customColor) { sidebar.style.backgroundColor = customColor; customColorInput.value = customColor; } else { sidebar.classList.add(localStorage.getItem('cl') ?? 'bg-gradient-primary'); }
            customColorInput.addEventListener('input', function () { sidebar.classList.remove('bg-gradient-primary', 'bg-gradient-dark', 'bg-gradient-success', 'bg-gradient-info', 'bg-gradient-warning', 'bg-gradient-danger'); sidebar.style.backgroundColor = this.value; localStorage.setItem('sidebarCustomColor', this.value); localStorage.removeItem('cl'); });
        }
    </script>
    <script>
        let userId = '{{ Auth::id() }}'
    </script>
    <script>
    const settingsDropdown = document.getElementById('settingsDropdown');
    if (settingsDropdown) settingsDropdown.addEventListener('click', function(e) {
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


