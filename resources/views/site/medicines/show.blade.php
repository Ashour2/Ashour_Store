@extends('site.layout')

@section('title', $medicine->trans_name)

@section('content')
@php
    $wa = preg_replace('/[^0-9]/', '', setting('whatsapp_number'));
    $rtl = LaravelLocalization::getCurrentLocaleDirection() == 'rtl';
@endphp
<div class="container py-5">
    <nav class="mb-4 small">
        <a href="{{ route('home') }}" class="text-muted">{{ __('site.home') }}</a>
        <span class="mx-1">/</span>
        <a href="{{ route('medicines.index') }}" class="text-muted">{{ __('site.medicines') }}</a>
        <span class="mx-1">/</span>
        <span class="text-ph">{{ $medicine->trans_name }}</span>
    </nav>

    <div class="row g-4">
        {{-- Image + gallery --}}
        <div class="col-lg-5">
            <div class="bg-white border rounded-4 p-3 text-center">
                <img id="mainImg" src="{{ $medicine->img_path }}" alt="{{ $medicine->trans_name }}"
                     class="img-fluid rounded-3" style="max-height: 360px; object-fit: contain;">
            </div>
            @if ($medicine->gallery->count())
                <div class="d-flex gap-2 mt-3 flex-wrap">
                    <img src="{{ $medicine->img_path }}" class="rounded border p-1" width="70" height="70"
                         style="object-fit: cover; cursor:pointer" onclick="document.getElementById('mainImg').src=this.src">
                    @foreach ($medicine->gallery as $img)
                        <img src="{{ asset('images/'.$img->path) }}" class="rounded border p-1" width="70" height="70"
                             style="object-fit: cover; cursor:pointer" onclick="document.getElementById('mainImg').src=this.src">
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Details --}}
        <div class="col-lg-7">
            <div class="d-flex gap-2 mb-2">
                @if ($medicine->category_id)
                    <span class="badge bg-light text-ph">{{ $medicine->category->trans_name }}</span>
                @endif
                @if ($medicine->requires_prescription)
                    <span class="badge rx-badge"><i class="fas fa-prescription"></i> {{ __('site.requires_prescription') }}</span>
                @endif
            </div>

            <h2 class="fw-bold">{{ $medicine->trans_name }}</h2>
            <div class="d-flex align-items-center gap-3 mb-3">
                @if ($medicine->show_price)
                    <div class="h4 text-ph mb-0">{{ number_format($medicine->price, 2) }}</div>
                @endif
                @include('site.partials.stock-badge')
            </div>

            <table class="table table-sm">
                <tbody>
                    @if ($medicine->active_ingredient)
                        <tr><th class="text-muted" style="width:40%">{{ __('site.active_ingredient') }}</th><td>{{ $medicine->active_ingredient }}</td></tr>
                    @endif
                    @if ($medicine->scientific_name)
                        <tr><th class="text-muted">{{ __('site.scientific_name') }}</th><td>{{ $medicine->scientific_name }}</td></tr>
                    @endif
                    @if ($medicine->trade_name)
                        <tr><th class="text-muted">{{ __('site.trade_name') }}</th><td>{{ $medicine->trade_name }}</td></tr>
                    @endif
                    @if ($medicine->dosage_form)
                        <tr><th class="text-muted">{{ __('site.dosage_form') }}</th><td>{{ $medicine->dosage_form }}</td></tr>
                    @endif
                    @if ($medicine->trans_contraindications_warnings)
                        <tr><th class="text-muted">{{ __('site.contraindications_warnings') }}</th><td>{{ $medicine->trans_contraindications_warnings }}</td></tr>
                    @endif
                    @if ($medicine->expiry_date)
                        <tr><th class="text-muted">{{ __('site.expiry_date') }}</th><td>{{ $medicine->expiry_date->format('Y-m-d') }}</td></tr>
                    @endif
                </tbody>
            </table>

            @if ($medicine->trans_description)
                <h6 class="fw-bold mt-3">{{ __('site.description') }}</h6>
                <p class="text-muted">{{ $medicine->trans_description }}</p>
            @endif

            @php $can_order = ! ($medicine->show_stock && ! $medicine->in_stock); @endphp
            @if ($can_order)
                <div class="d-flex align-items-center gap-3 mt-4">
                    <label class="text-muted small mb-0" for="detailQty">{{ __('site.quantity') }}</label>
                    <input type="number" id="detailQty" class="form-control" value="1" min="1" max="99" style="width: 90px;">
                    <button type="button" class="btn btn-ph btn-lg rounded-pill px-4"
                            data-cart-add="{{ $medicine->id }}" data-qty-from="detailQty">
                        <i class="fas fa-cart-plus"></i> {{ __('site.add_to_cart') }}
                    </button>
                </div>
            @endif

            <div class="d-flex gap-2 mt-3 flex-wrap">
                <a href="{{ route('consultation.create', ['medicine' => $medicine->id]) }}" class="btn btn-ph btn-lg rounded-pill px-4">
                    <i class="fas fa-comment-medical"></i> {{ __('site.ask_about_medicine') }}
                </a>
                @if ($wa)
                    <a target="_blank" class="btn btn-success btn-lg rounded-pill px-4"
                       href="https://wa.me/{{ $wa }}?text={{ urlencode(($rtl ? 'استفسار عن دواء: ' : 'Question about: ') . $medicine->trans_name) }}">
                        <i class="fab fa-whatsapp"></i> {{ __('site.whatsapp') }}
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Related --}}
    @if ($related->count())
        <hr class="my-5">
        <h4 class="fw-bold mb-4">{{ __('site.related_medicines') }}</h4>
        <div class="row g-3">
            @foreach ($related as $item)
                <div class="col-6 col-md-4 col-lg-3">
                    @include('site.partials.medicine-card', ['medicine' => $item])
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
