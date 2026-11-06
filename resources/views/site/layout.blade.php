@php
    $locale = app()->getLocale();
    $rtl = LaravelLocalization::getCurrentLocaleDirection() == 'rtl';
    $wa = preg_replace('/[^0-9]/', '', setting('whatsapp_number'));
    $logo = logo_url();

    // Slider: the colour gradient is always the base. Any pictures chosen in the
    // dashboard are faded over it by site.partials.hero-slides on the homepage.
    $hero_slides = setting('hero_mode') === 'image' ? hero_images() : [];

    // The colours go straight into a <style> rule, where HTML escaping would
    // not protect anything, so they are re-checked here.
    $hex = fn ($key, $fallback) => preg_match('/^#[0-9a-fA-F]{6}$/', (string) setting($key))
        ? setting($key)
        : $fallback;

    $hero_background = sprintf(
        'linear-gradient(135deg, %s, %s)',
        $hex('hero_color_from', '#0f766e'),
        $hex('hero_color_to', '#0ea5a4')
    );
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $rtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@hasSection('title')@yield('title') — @endif{{ tsetting('site_name') }}</title>
    @if ($logo)
        <link rel="icon" href="{{ $logo }}">
        <link rel="apple-touch-icon" href="{{ $logo }}">
    @endif

    @if ($rtl)
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    @else
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --ph-primary: #0ea5a4;
            --ph-primary-dark: #0f766e;
            --ph-accent: #22c55e;
            --ph-ink: #0f172a;
            --ph-soft: #f0fdfa;
        }
        body {
            font-family: {{ $rtl ? "'Cairo'" : "'Poppins'" }}, system-ui, sans-serif;
            color: var(--ph-ink);
            background: #f8fafc;
        }
        a { text-decoration: none; }
        .navbar-brand { font-weight: 800; color: var(--ph-primary-dark) !important; }
        .navbar-brand i { color: var(--ph-primary); }
        .brand-logo { height: 40px; width: auto; max-width: 180px; object-fit: contain; }
        @media (max-width: 576px) { .brand-logo { height: 34px; max-width: 130px; } }
        /* The footer is dark, so the logo sits on a light chip to stay readable */
        .footer-logo {
            display: inline-flex; align-items: center; justify-content: center;
            background: #fff; border-radius: .6rem; padding: .4rem .6rem; margin-bottom: .75rem;
        }
        .footer-logo img { height: 46px; width: auto; max-width: 190px; object-fit: contain; }
        .btn-ph { background: var(--ph-primary); border-color: var(--ph-primary); color: #fff; }
        .btn-ph:hover { background: var(--ph-primary-dark); border-color: var(--ph-primary-dark); color: #fff; }
        .text-ph { color: var(--ph-primary-dark); }
        .hero {
            background: {!! $hero_background !!};
            color: #fff;
            border-radius: 0 0 2rem 2rem;
            position: relative;
            overflow: hidden;
            @if ($hero_slides) text-shadow: 0 1px 4px rgba(0,0,0,.45); @endif
        }
        /* Keep the wording above the pictures fading behind it */
        .hero > .container { position: relative; z-index: 2; }
        .hero .search-box input { height: 54px; border-radius: 999px; }
        .med-card {
            background: #fff; border: 1px solid #e2e8f0; border-radius: 1rem;
            overflow: hidden; transition: .2s; height: 100%;
        }
        .med-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(15,118,110,.12); }
        .med-card img { height: 170px; object-fit: cover; width: 100%; background: var(--ph-soft); }
        .category-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: .85rem;
        }
        .cat-pill {
            display: flex; min-height: 126px; padding: 1rem .75rem; align-items: center;
            justify-content: center; flex-direction: column; gap: .45rem; text-align: center;
            background: #fff; border: 1px solid #e2e8f0; border-radius: 1rem;
            color: var(--ph-ink); transition: .2s;
        }
        .cat-pill:hover { border-color: var(--ph-primary); color: var(--ph-primary-dark); transform: translateY(-3px); box-shadow: 0 8px 18px rgba(15,118,110,.12); }
        .cat-icon { color: var(--ph-primary); font-size: 1.75rem; line-height: 1; height: 34px; display: flex; align-items: center; }
        .category-line-icon { width: 34px; height: 34px; display: block; }
        .category-line-icon-sm { width: 24px; height: 24px; vertical-align: middle; }
        .cat-name { font-weight: 700; font-size: .9rem; }
        .cat-pill .badge { font-size: .7rem; }
        .rx-badge { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; font-size: .72rem; }
        .wa-float {
            position: fixed; bottom: 22px; {{ $rtl ? 'left' : 'right' }}: 22px; z-index: 999;
            width: 58px; height: 58px; border-radius: 50%; background: #25d366; color: #fff;
            display: flex; align-items: center; justify-content: center; font-size: 28px;
            box-shadow: 0 8px 20px rgba(37,211,102,.4);
        }
        .wa-float:hover { color: #fff; transform: scale(1.05); }
        /* Floating social rail */
        .social-rail {
            position: fixed; top: 50%; transform: translateY(-50%);
            {{ $rtl ? 'left' : 'right' }}: 14px; z-index: 998;
            display: flex; flex-direction: column; gap: 10px;
        }
        .social-rail-btn {
            width: 46px; height: 46px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            background: var(--sc, #0ea5a4); color: #fff; font-size: 20px;
            box-shadow: 0 6px 16px rgba(0,0,0,.18); transition: .2s;
        }
        .social-rail-btn:hover { color: #fff; transform: scale(1.12); }
        @media (max-width: 576px) {
            .social-rail { {{ $rtl ? 'left' : 'right' }}: 8px; gap: 7px; }
            .social-rail-btn { width: 38px; height: 38px; font-size: 16px; }
        }
        /* ===== Floating cart ===== */
        .cart-float {
            position: fixed; bottom: 22px; {{ $rtl ? 'right' : 'left' }}: 22px; z-index: 999;
            width: 58px; height: 58px; border-radius: 50%; border: 0;
            background: var(--ph-primary); color: #fff; font-size: 24px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 8px 20px rgba(14,165,164,.45); transition: .2s;
        }
        .cart-float:hover { background: var(--ph-primary-dark); transform: scale(1.06); color: #fff; }
        .cart-float-badge {
            position: absolute; top: -4px; {{ $rtl ? 'left' : 'right' }}: -4px;
            min-width: 24px; height: 24px; padding: 0 6px; border-radius: 999px;
            background: #ef4444; color: #fff; font-size: 12px; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            border: 2px solid #fff; line-height: 1;
        }
        .cart-float.bump { animation: cartBump .45s ease; }
        @keyframes cartBump {
            0% { transform: scale(1); } 35% { transform: scale(1.22); }
            70% { transform: scale(.94); } 100% { transform: scale(1); }
        }
        .cart-panel { width: 380px; max-width: 92vw; }
        .bg-ph-soft { background: var(--ph-soft); }
        .cart-empty-icon {
            width: 74px; height: 74px; border-radius: 50%; margin: 0 auto;
            background: var(--ph-soft); color: var(--ph-primary); font-size: 30px;
            display: flex; align-items: center; justify-content: center;
        }
        .cart-line { display: flex; gap: .75rem; padding: .85rem 1rem; border-bottom: 1px solid #f1f5f9; }
        .cart-line img { width: 62px; height: 62px; object-fit: cover; border-radius: .6rem; background: var(--ph-soft); flex: 0 0 auto; }
        .cart-line-title { font-size: .9rem; font-weight: 600; color: var(--ph-ink); display: block; }
        .cart-line-title:hover { color: var(--ph-primary-dark); }
        .qty-box { display: inline-flex; align-items: center; border: 1px solid #e2e8f0; border-radius: 999px; overflow: hidden; }
        .qty-box button {
            border: 0; background: #fff; width: 28px; height: 28px; color: var(--ph-primary-dark);
            display: flex; align-items: center; justify-content: center; font-size: 12px;
        }
        .qty-box button:hover { background: var(--ph-soft); }
        .qty-box span { min-width: 30px; text-align: center; font-size: .85rem; font-weight: 600; }
        .btn-cart-add {
            width: 38px; height: 38px; border-radius: 50%; border: 0; flex: 0 0 auto;
            background: var(--ph-soft); color: var(--ph-primary-dark); font-size: 15px;
            display: inline-flex; align-items: center; justify-content: center; transition: .2s;
        }
        .btn-cart-add:hover { background: var(--ph-primary); color: #fff; }
        .btn-cart-add:disabled { opacity: .45; cursor: not-allowed; }
        .cart-toast {
            position: fixed; bottom: 92px; {{ $rtl ? 'right' : 'left' }}: 22px; z-index: 1100;
            background: var(--ph-ink); color: #fff; padding: .6rem 1rem; border-radius: .7rem;
            font-size: .87rem; box-shadow: 0 8px 22px rgba(0,0,0,.25);
            opacity: 0; transform: translateY(10px); pointer-events: none; transition: .25s;
        }
        .cart-toast.show { opacity: 1; transform: translateY(0); }
        .cart-toast.danger { background: #b91c1c; }
        @media (max-width: 576px) {
            .cart-float { width: 50px; height: 50px; font-size: 20px; bottom: 18px; {{ $rtl ? 'right' : 'left' }}: 14px; }
            .cart-toast { bottom: 78px; {{ $rtl ? 'right' : 'left' }}: 14px; max-width: 70vw; }
        }
        footer { background: var(--ph-ink); color: #cbd5e1; }
        footer a { color: #cbd5e1; }
        footer a:hover { color: #fff; }
    /* ===== Refined landing page ===== */
body{background:#f7f9fc}.site-navbar{background:rgba(255,255,255,.96)!important;border-bottom:1px solid #e8edf3;box-shadow:none!important;min-height:76px;backdrop-filter:blur(10px)}.site-brand{font-size:1rem;color:#12302e!important}.site-brand span{white-space:nowrap}.brand-logo{height:44px;max-width:150px}.site-toggler{border:1px solid #d8e1e8;color:var(--ph-primary-dark);border-radius:9px;padding:6px 10px}.site-toggler:focus{box-shadow:0 0 0 .2rem rgba(14,165,164,.15)}.site-nav-links{gap:5px}.site-nav-links .nav-link{color:#405265!important;font-size:.9rem;font-weight:700;padding:8px 11px!important;border-radius:8px}.site-nav-links .nav-link:hover{background:#eef9f8;color:var(--ph-primary-dark)!important}.site-nav-actions{display:flex;align-items:center;gap:8px;white-space:nowrap}.nav-icon-button,.nav-login,.nav-pharmacist{display:inline-flex;align-items:center;justify-content:center;gap:6px;border-radius:9px;padding:8px 11px;font-size:.78rem;font-weight:700;transition:.18s}.nav-icon-button{color:#536273;border:1px solid #e0e6ed}.nav-icon-button:hover{color:var(--ph-primary-dark);border-color:#a5dfdb;background:#f3fbfb}.nav-login{color:#137c77;border:1px solid #82cbc6}.nav-login:hover{background:#effafa;color:#0e615d}.nav-pharmacist{color:#fff;background:#16835c;border:1px solid #16835c;box-shadow:0 4px 10px rgba(22,131,92,.16)}.nav-pharmacist:hover{background:#106e4c;color:#fff}.home-hero{min-height:480px;margin:0 0 50px;border-radius:0 0 34px 34px;display:flex;align-items:center;background:linear-gradient(135deg,#0b7774,#10a7a2)!important}.hero-shape{position:absolute;border-radius:50%;background:rgba(255,255,255,.1);z-index:1}.hero-shape-one{width:300px;height:300px;top:-160px;right:8%}.hero-shape-two{width:240px;height:240px;bottom:-145px;left:13%}.hero-inner{position:relative;z-index:2;padding:62px 12px}.hero-panel{max-width:760px;margin:auto;padding:30px}.hero-eyebrow{display:inline-flex;align-items:center;gap:7px;background:rgba(255,255,255,.16);border:1px solid rgba(255,255,255,.26);border-radius:999px;padding:7px 13px;font-size:.78rem;font-weight:700;margin-bottom:16px}.hero-panel h1{font-size:clamp(1.85rem,4vw,3.15rem);font-weight:800;line-height:1.35;margin:0 0 12px}.hero-panel p{max-width:600px;margin:0 auto 25px;font-size:1.02rem;line-height:1.9;color:rgba(255,255,255,.9)}.hero .search-box{max-width:650px}.hero .search-box .input-group{background:#fff;border-radius:13px;padding:5px;box-shadow:0 14px 28px rgba(4,61,59,.24)}.hero .search-box input{height:48px;border-radius:9px!important;font-size:.9rem;box-shadow:none!important}.hero .search-box .input-group-text{border:0;background:#fff;color:#16918c;padding:0 12px}.hero .search-box button{border:0;border-radius:9px;background:#0b7774;color:#fff;font-weight:700;padding:0 22px}.hero-actions{display:flex;gap:10px;justify-content:center;flex-wrap:wrap;margin-top:18px}.hero-actions .btn{border-radius:9px;padding:10px 17px;font-size:.86rem;font-weight:700}.hero-primary{background:#fff;color:#0f5f5b}.hero-primary:hover{background:#e9fffd;color:#0b5753}.hero-secondary{color:#fff;border:1px solid rgba(255,255,255,.7)}.hero-secondary:hover{background:rgba(255,255,255,.12);color:#fff}.home-section{padding-top:22px;padding-bottom:48px}.section-heading{display:flex;align-items:end;justify-content:space-between;gap:16px;margin-bottom:22px}.section-heading>div>span{display:block;color:#15918c;font-size:.76rem;font-weight:800;margin-bottom:4px}.section-heading h2{font-size:1.48rem;font-weight:800;margin:0;color:#152b3a}.section-heading h2 i{color:#12938e}.section-heading p{font-size:.85rem;color:#6d7b87;margin:5px 0 0}.section-heading>a{color:#147d78;font-size:.82rem;font-weight:800;white-space:nowrap}.category-grid{grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:14px}.cat-pill{min-height:174px;align-items:flex-start;justify-content:flex-start;padding:17px;border-radius:15px;gap:9px;text-align:right;border:1px solid #e3eaf0;box-shadow:0 3px 10px rgba(32,55,74,.025)}.cat-pill:hover{border-color:#32b8b2;box-shadow:0 13px 26px rgba(15,118,110,.1);transform:translateY(-4px)}.cat-icon{width:46px;height:46px;padding:4px;border-radius:12px;background:#eafafa}.cat-icon img{width:100%;height:100%;object-fit:cover;border-radius:9px}.cat-name{font-size:.91rem}.cat-count{font-size:.7rem;color:#71808d;background:#f2f5f8;border-radius:999px;padding:3px 8px}.featured-section{padding-top:10px}.med-card{border-radius:15px;border-color:#e3eaf0;box-shadow:0 3px 10px rgba(32,55,74,.025)}.med-card img{height:190px;background:#f1f8f8}.med-card .p-3{padding:15px!important}.med-card h6{font-size:.92rem;font-weight:800}.article-zone{background:#edf8f7;border-top:1px solid #d9efed;border-bottom:1px solid #d9efed}.article-card{background:#fff;border:1px solid #deeceb;border-radius:15px;overflow:hidden;color:#1d2c3b;transition:.2s;box-shadow:0 3px 10px rgba(32,55,74,.025)}.article-card:hover{transform:translateY(-4px);color:#1d2c3b;box-shadow:0 13px 26px rgba(15,118,110,.1)}.article-card img{height:190px;width:100%;object-fit:cover;background:#e5f2f1}.article-card>div{padding:16px}.article-label{color:#168d87;background:#e8f8f6;border-radius:999px;padding:3px 8px;font-size:.68rem;font-weight:800}.article-card h3{font-size:1rem;font-weight:800;margin:11px 0 7px}.article-card p{font-size:.82rem;color:#687784;line-height:1.7;margin-bottom:12px}.article-card b{font-size:.78rem;color:#0f7772}
@media(max-width:991.98px){.site-navbar .container{padding-top:9px;padding-bottom:9px}.site-nav-links{padding:12px 0 5px}.site-nav-actions{padding:8px 0 2px;flex-wrap:wrap}.home-hero{min-height:430px}.hero-inner{padding:48px 12px}.category-grid{grid-template-columns:repeat(3,minmax(0,1fr))}}
@media(max-width:575.98px){.site-navbar{min-height:64px}.brand-logo{height:35px;max-width:120px}.site-brand{font-size:.84rem}.site-nav-actions{gap:6px}.nav-icon-button,.nav-login,.nav-pharmacist{padding:7px 9px;font-size:.73rem}.nav-icon-button span{display:none}.home-hero{min-height:410px;margin-bottom:32px;border-radius:0 0 24px 24px}.hero-inner{padding:36px 8px}.hero-panel{padding:20px 6px}.hero-panel p{font-size:.88rem;line-height:1.7}.hero .search-box .input-group{border-radius:11px}.hero .search-box input{height:45px;padding:0 5px;font-size:.77rem}.hero .search-box button{padding:0 12px;font-size:.78rem}.hero-actions .btn{padding:9px 12px;font-size:.77rem}.home-section{padding-bottom:34px}.section-heading{align-items:flex-end;margin-bottom:16px}.section-heading h2{font-size:1.2rem}.section-heading>a{font-size:.7rem}.category-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:9px}.cat-pill{min-height:138px;padding:13px}.cat-icon{width:38px;height:38px}.cat-name{font-size:.78rem}.med-card img{height:135px}.med-card .p-3{padding:11px!important}.med-card h6{font-size:.78rem}.med-card .btn{font-size:.66rem;padding:5px 8px}.article-card img{height:160px}}.quick-category-bar{position:relative;z-index:1015;background:#fff;border-bottom:1px solid #e5ebf0}.quick-category-inner{min-height:48px;display:flex;align-items:center;gap:16px}.quick-category-title{font-size:.77rem;font-weight:800;color:#166b67;white-space:nowrap}.quick-category-title i{color:#16a39d;margin-left:4px}.quick-category-links{display:flex;align-items:center;gap:4px;overflow-x:auto;scrollbar-width:none;flex:1}.quick-category-links::-webkit-scrollbar{display:none}.quick-category-links>a,.quick-more-button{border:0;background:transparent;color:#536273;font-size:.76rem;font-weight:700;padding:7px 9px;border-radius:7px;white-space:nowrap}.quick-category-links>a:hover,.quick-more-button:hover{background:#edf9f8;color:#0f7772}.quick-more-button i{font-size:.58rem;margin-right:3px}.quick-more-menu{border:1px solid #e2e8ee;border-radius:10px;box-shadow:0 10px 25px rgba(16,44,60,.14);padding:6px;min-width:190px}.quick-more-menu .dropdown-item{border-radius:6px;padding:8px 10px;font-size:.8rem;color:#415365}.quick-more-menu .dropdown-item:hover{background:#effafa;color:#0e7771}@media(max-width:575.98px){.quick-category-inner{gap:8px;min-height:43px}.quick-category-title{font-size:.7rem}.quick-category-links>a,.quick-more-button{font-size:.69rem;padding:6px 8px}}.medicine-gallery-zone{padding-top:18px;padding-bottom:20px}.category-medicine-widget{padding:26px 0 38px;border-bottom:1px solid #e7edf2}.category-medicine-widget:last-child{border-bottom:0}.gallery-widget-head{display:flex;align-items:center;justify-content:space-between;gap:14px;margin-bottom:20px}.gallery-category-title{display:flex;align-items:center;gap:11px}.gallery-category-image{width:48px;height:48px;display:grid;place-items:center;padding:4px;background:#e9f8f7;border-radius:13px}.gallery-category-image img{width:100%;height:100%;object-fit:cover;border-radius:9px}.gallery-category-title span:not(.gallery-category-image){display:block;color:#178a84;font-size:.72rem;font-weight:800;margin-bottom:2px}.gallery-category-title h2{font-size:1.28rem;font-weight:800;margin:0;color:#152b3a}.gallery-widget-head>a{color:#137d77;font-size:.8rem;font-weight:800;white-space:nowrap}.category-medicine-widget .med-card{height:100%}@media(max-width:575.98px){.medicine-gallery-zone{padding-top:10px}.category-medicine-widget{padding:21px 0 29px}.gallery-category-image{width:40px;height:40px}.gallery-category-title h2{font-size:1.05rem}.gallery-widget-head>a{font-size:.68rem}}.gallery-random-icon{color:#0f7772;font-size:1.2rem}.medicine-gallery-grid{--bs-gutter-x:1rem;--bs-gutter-y:1rem}.medicine-gallery-grid>.col-lg{flex:0 0 20%;max-width:20%}.medicine-gallery-grid .med-card img{height:140px}.medicine-gallery-grid .med-card .p-3{padding:11px!important}.medicine-gallery-grid .med-card h6{font-size:.78rem}.medicine-gallery-grid .med-card .btn{font-size:.64rem;padding:5px 7px}.medicine-gallery-grid .btn-cart-add{width:31px;height:31px;font-size:.72rem}@media(max-width:991.98px){.medicine-gallery-grid>.col-lg{flex:0 0 33.333%;max-width:33.333%}}@media(max-width:575.98px){.medicine-gallery-grid>.col-lg{flex:0 0 50%;max-width:50%}.medicine-gallery-grid .med-card img{height:130px}}.article-body{line-height:2}.article-font-cairo{font-family:'Cairo',sans-serif}.article-font-tajawal{font-family:'Tajawal','Cairo',sans-serif}.article-font-serif{font-family:Georgia,'Times New Roman',serif}.article-size-small{font-size:.95rem}.article-size-medium{font-size:1.08rem}.article-size-large{font-size:1.25rem}.article-size-xlarge{font-size:1.45rem}</style>
    @stack('styles')
</head>
<body>
    @include('site.partials.navbar')

    <main>
        @yield('content')
    </main>

    @include('site.partials.footer')

    @include('site.partials.join-modal')

    @if ($wa)
        <a class="wa-float" target="_blank" href="https://wa.me/{{ $wa }}" title="WhatsApp">
            <i class="fab fa-whatsapp"></i>
        </a>
    @endif

    @include('site.partials.social')

    @include('site.partials.cart-float')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @include('site.partials.cart-scripts')
    @stack('scripts')
</body>
</html>
