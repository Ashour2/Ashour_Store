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
    </style>
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
