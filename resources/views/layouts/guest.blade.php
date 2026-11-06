<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ tsetting('site_name', config('app.name', 'Line Pharm')) }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net"><link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet"><script src="https://cdn.tailwindcss.com"></script>
<style>
    input[type="text"], input[type="email"], input[type="tel"], input[type="password"], textarea, select {
        border: 1.5px solid #b8c7d2 !important;
        border-radius: .65rem !important;
        background: #fff !important;
        box-shadow: none !important;
    }
    input[type="text"]:focus, input[type="email"]:focus, input[type="tel"]:focus, input[type="password"]:focus, textarea:focus, select:focus {
        border-color: #0f9b94 !important;
        box-shadow: 0 0 0 3px rgba(15,155,148,.12) !important;
    }
</style></head>
<body class="font-sans text-gray-900 antialiased" style="font-family:Cairo,system-ui,sans-serif">
    <div class="min-h-screen flex flex-col items-center justify-center px-4 py-8" style="background:linear-gradient(135deg,#f0fdfa,#f8fafc 55%,#ecfdf5)">
        <a href="{{ route('home') }}" class="mb-5 flex flex-col items-center text-center">
            @if ($logo = logo_url())<img src="{{ $logo }}" alt="{{ tsetting('site_name') }}" class="h-16 w-auto max-w-[210px] object-contain">@else <x-application-logo class="w-14 h-14 fill-current text-teal-700" />@endif
            <span class="mt-2 text-sm font-bold text-slate-600">{{ tsetting('site_name', 'Line Pharm') }}</span>
        </a>
        <div class="w-full max-w-2xl rounded-2xl border border-white bg-white/95 px-5 py-6 shadow-xl shadow-teal-900/5 sm:px-8">
            {{ $slot }}
        </div>
    </div>
</body>
</html>