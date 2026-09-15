@extends('admin.master')

@section('title', 'Line Pharm')

@section('content')
@php
    // Scattered rounded stat cards — varied sizes, colors, and slight random tilt.
    $cards = [
        ['label' => __('admin.medicines'),        'value' => $stats['medicines'],         'icon' => 'fa-capsules',        'grad' => 'linear-gradient(135deg,#0ea5a4,#0f766e)', 'size' => 'xl', 'rot' => -3, 'route' => route('admin.products.index'), 'can' => 'products.view'],
        ['label' => __('admin.consultations'),     'value' => $stats['consultations'],      'icon' => 'fa-comment-medical', 'grad' => 'linear-gradient(135deg,#8b5cf6,#6d28d9)', 'size' => 'lg', 'rot' => 2,  'route' => route('admin.consultations.index'), 'can' => 'consultations.view'],
        ['label' => __('admin.new'),               'value' => $stats['consultations_new'],  'icon' => 'fa-bell',            'grad' => 'linear-gradient(135deg,#f43f5e,#be123c)', 'size' => 'sm', 'rot' => -5, 'route' => route('admin.consultations.index'), 'can' => 'consultations.view'],
        ['label' => __('admin.categories'),        'value' => $stats['categories'],         'icon' => 'fa-th-large',        'grad' => 'linear-gradient(135deg,#3b82f6,#1d4ed8)', 'size' => 'md', 'rot' => 4,  'route' => route('admin.categories.index'), 'can' => 'categories.view'],
        ['label' => __('admin.requires_prescription'), 'value' => $stats['prescription'],   'icon' => 'fa-prescription',    'grad' => 'linear-gradient(135deg,#f59e0b,#b45309)', 'size' => 'md', 'rot' => -2, 'route' => route('admin.products.index'), 'can' => 'products.view'],
        ['label' => __('admin.out_of_stock'),      'value' => $stats['out_of_stock'],       'icon' => 'fa-box-open',        'grad' => 'linear-gradient(135deg,#64748b,#334155)', 'size' => 'sm', 'rot' => 5,  'route' => route('admin.products.index'), 'can' => 'products.view'],
        ['label' => __('admin.orders'),            'value' => $stats['orders'],             'icon' => 'fa-receipt',         'grad' => 'linear-gradient(135deg,#14b8a6,#0d9488)', 'size' => 'lg', 'rot' => 3,  'route' => route('admin.orders.index'), 'can' => 'orders.view'],
        ['label' => __('admin.new_orders'),         'value' => $stats['orders_new'],         'icon' => 'fa-cart-shopping',   'grad' => 'linear-gradient(135deg,#ec4899,#9d174d)', 'size' => 'sm', 'rot' => -4, 'route' => route('admin.orders.index', ['status' => 'new']), 'can' => 'orders.view'],
        ['label' => __('admin.customers'),         'value' => $stats['users'],              'icon' => 'fa-users',           'grad' => 'linear-gradient(135deg,#22c55e,#15803d)', 'size' => 'lg', 'rot' => -4, 'route' => route('admin.customers'), 'can' => 'customers.view'],
    ];

    // Only show a card the signed-in user is allowed to open
    $cards = array_values(array_filter($cards, fn ($c) => auth()->user()->can($c['can'])));
@endphp

<style>
    .bento-head { padding: 24px 8px 4px; }
    .bento-head h2 { font-weight: 800; margin: 0; }
    .bento-wrap {
        display: flex; flex-wrap: wrap; gap: 22px;
        align-items: center; justify-content: center;
        padding: 24px 12px 40px; max-width: 1100px; margin: 0 auto;
    }
    .bento {
        color: #fff; border-radius: 26px; position: relative;
        box-shadow: 0 14px 30px rgba(2,6,23,.16);
        display: flex; flex-direction: column; justify-content: space-between;
        text-decoration: none; overflow: hidden;
        transition: transform .25s ease, box-shadow .25s ease;
    }
    .bento:hover { transform: translateY(-6px) rotate(0deg) scale(1.03) !important; box-shadow: 0 22px 40px rgba(2,6,23,.28); color:#fff; }
    .bento .ic { position: absolute; opacity: .18; font-size: 6rem; top: -10px; inset-inline-end: -8px; }
    .bento .val { font-size: 2.6rem; font-weight: 800; line-height: 1; }
    .bento .lbl { font-size: .95rem; opacity: .95; margin-top: 6px; }
    /* Sizes */
    .bento.xl { width: 340px; height: 200px; padding: 26px; }
    .bento.lg { width: 250px; height: 190px; padding: 24px; }
    .bento.md { width: 210px; height: 150px; padding: 22px; }
    .bento.sm { width: 165px; height: 150px; padding: 20px; border-radius: 50% 50% 50% 50% / 45% 45% 55% 55%; }
    .bento.xl .val { font-size: 3.4rem; }
    .bento.sm .val { font-size: 2rem; }
    /* Info pills */
    .info-pill {
        background: #fff; border: 1px solid #e2e8f0; border-radius: 999px;
        padding: 12px 22px; display: inline-flex; align-items: center; gap: 10px;
        box-shadow: 0 8px 18px rgba(2,6,23,.08); color: #0f172a; font-weight: 600;
    }
    .info-pill i { color: #0ea5a4; }
    @media (max-width: 767px) {
        .bento { transform: none !important; width: 100% !important; height: auto !important; min-height: 130px; }
    }
</style>

<div class="bento-head text-center">
    <h2>👋 {{ __('admin.dash') }} — {{ tsetting('site_name', 'Line Pharm') }}</h2>
    <p class="text-muted">{{ now()->format('l, d M Y') }}</p>
</div>

{{-- Scattered stat cards --}}
<div class="bento-wrap">
    @foreach ($cards as $c)
        <a href="{{ $c['route'] }}" class="bento {{ $c['size'] }}" style="background: {{ $c['grad'] }}; transform: rotate({{ $c['rot'] }}deg);">
            <i class="fas {{ $c['icon'] }} ic"></i>
            <div class="val">{{ $c['value'] }}</div>
            <div class="lbl">{{ $c['label'] }}</div>
        </a>
    @endforeach
</div>

{{-- Site info pills (scattered) --}}
<div class="bento-wrap" style="padding-top: 0;">
    @if (setting('whatsapp_number'))
        <a class="info-pill" target="_blank" href="https://wa.me/{{ preg_replace('/[^0-9]/','',setting('whatsapp_number')) }}" style="transform: rotate(-2deg);">
            <i class="fab fa-whatsapp" style="color:#25d366"></i> {{ setting('whatsapp_number') }}
        </a>
    @endif
    @if (setting('phone'))
        <span class="info-pill" style="transform: rotate(3deg);"><i class="fas fa-phone"></i> {{ setting('phone') }}</span>
    @endif
    @if (tsetting('working_hours'))
        <span class="info-pill" style="transform: rotate(-3deg);"><i class="fas fa-clock"></i> {{ tsetting('working_hours') }}</span>
    @endif
    <a class="info-pill" target="_blank" href="{{ route('home') }}" style="transform: rotate(2deg);">
        <i class="fas fa-globe"></i> {{ __('admin.home') }}
    </a>
    @can('settings.manage')
        <a class="info-pill" href="{{ route('admin.settings.edit') }}" style="transform: rotate(-1deg);">
            <i class="fas fa-sliders-h"></i> {{ __('admin.site_settings') }}
        </a>
    @endcan
</div>

{{-- Latest orders --}}
@can('orders.view')
<div class="row px-3" style="max-width:1100px;margin:0 auto;">
    <div class="col-12 mb-3">
        <div class="card" style="border-radius:20px;overflow:hidden;">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <strong><i class="fas fa-receipt" style="color:#14b8a6"></i> {{ __('admin.orders') }}</strong>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-secondary">{{ __('admin.all_orders') }}</a>
            </div>
            <div class="list-group list-group-flush">
                @forelse ($latest_orders as $o)
                    <a href="{{ route('admin.orders.show', $o->id) }}" class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                        <span>
                            <span dir="ltr" class="text-muted small">{{ $o->order_number }}</span>
                            — {{ $o->customer_name }}
                            <small class="text-muted">({{ $o->order_details_count }} {{ __('admin.items') }})</small>
                        </span>
                        <span>
                            <strong class="mr-2">{{ number_format($o->total, 2) }}</strong>
                            <span class="badge badge-{{ $o->status_color }}">{{ __('admin.status_' . $o->status) }}</span>
                        </span>
                    </a>
                @empty
                    <div class="list-group-item text-center text-muted">{{ __('admin.no_data') }}</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endcan

{{-- Latest lists --}}
<div class="row px-3 pb-5" style="max-width:1100px;margin:0 auto;">
    @can('consultations.view')
    <div class="col-md-6 mb-3">
        <div class="card" style="border-radius:20px;overflow:hidden;">
            <div class="card-header bg-white"><strong><i class="fas fa-comment-medical text-purple"></i> {{ __('admin.consultations') }}</strong></div>
            <div class="list-group list-group-flush">
                @forelse ($latest_consultations as $c)
                    <a href="{{ route('admin.consultations.show', $c->id) }}" class="list-group-item d-flex justify-content-between align-items-center">
                        <span>{{ $c->name }} — <small class="text-muted">{{ $c->phone }}</small></span>
                        @if ($c->status == 'new')<span class="badge badge-warning">{{ __('admin.new') }}</span>@else<span class="badge badge-success">{{ __('admin.answered') }}</span>@endif
                    </a>
                @empty
                    <div class="list-group-item text-center text-muted">{{ __('admin.no_data') }}</div>
                @endforelse
            </div>
        </div>
    </div>
    @endcan
    @can('products.view')
    <div class="col-md-6 mb-3">
        <div class="card" style="border-radius:20px;overflow:hidden;">
            <div class="card-header bg-white"><strong><i class="fas fa-capsules text-ph" style="color:#0ea5a4"></i> {{ __('admin.medicines') }}</strong></div>
            <div class="list-group list-group-flush">
                @forelse ($latest_medicines as $m)
                    <a href="{{ route('admin.products.edit', $m->id) }}" class="list-group-item d-flex justify-content-between align-items-center">
                        <span>{{ $m->trans_name }}</span>
                        <small class="text-muted">{{ $m->category->trans_name }}</small>
                    </a>
                @empty
                    <div class="list-group-item text-center text-muted">{{ __('admin.no_data') }}</div>
                @endforelse
            </div>
        </div>
    </div>
    @endcan
</div>
@endsection
