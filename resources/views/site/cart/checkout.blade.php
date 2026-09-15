@extends('site.layout')

@section('title', __('site.checkout'))

@section('content')
@php
    $prices_visible = cart()->pricesVisible();

    // Medicines in the basket the pharmacist will ask a prescription for
    $rx_items = $items->filter(fn ($item) => $item->product->requires_prescription);
@endphp
<div class="container py-5">
    <nav class="mb-4 small">
        <a href="{{ route('home') }}" class="text-muted">{{ __('site.home') }}</a>
        <span class="mx-1">/</span>
        <a href="{{ route('medicines.index') }}" class="text-muted">{{ __('site.medicines') }}</a>
        <span class="mx-1">/</span>
        <span class="text-ph">{{ __('site.checkout') }}</span>
    </nav>

    <h2 class="fw-bold mb-4"><i class="fas fa-clipboard-check text-ph"></i> {{ __('site.checkout') }}</h2>

    @if ($rx_items->isNotEmpty())
        <div class="alert alert-danger border-0 rounded-4 d-flex gap-3 align-items-start" role="alert">
            <i class="fas fa-prescription fs-4 mt-1"></i>
            <div>
                <strong class="d-block mb-1">{{ __('site.rx_notice_title') }}</strong>
                <p class="mb-2 small">{{ __('site.rx_notice_body') }}</p>
                <ul class="mb-0 small fw-semibold">
                    @foreach ($rx_items as $item)
                        <li>{{ $item->product->trans_name }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form action="{{ route('cart.store') }}" method="POST">
        @csrf
        <div class="row g-4">
            {{-- Customer details --}}
            <div class="col-lg-7">
                <div class="bg-white border rounded-4 p-4">
                    <h5 class="fw-bold mb-3">{{ __('site.order_details') }}</h5>

                    <div class="mb-3">
                        <label class="form-label">{{ __('site.your_name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="customer_name" value="{{ old('customer_name') }}"
                               class="form-control @error('customer_name') is-invalid @enderror" required>
                        @error('customer_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('site.your_phone') }} <span class="text-danger">*</span></label>
                        <input type="tel" name="customer_phone" value="{{ old('customer_phone') }}" dir="ltr"
                               class="form-control @error('customer_phone') is-invalid @enderror"
                               placeholder="{{ setting('default_country_code', '+966') }} 5xxxxxxxx" required>
                        @error('customer_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('site.address') }}</label>
                        <input type="text" name="customer_address" value="{{ old('customer_address') }}"
                               class="form-control @error('customer_address') is-invalid @enderror">
                        <small class="text-muted">{{ __('site.address_hint') }}</small>
                        @error('customer_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-2">
                        <label class="form-label">{{ __('site.order_note') }}</label>
                        <textarea name="note" rows="3"
                                  class="form-control @error('note') is-invalid @enderror">{{ old('note') }}</textarea>
                        <small class="text-muted">{{ __('site.order_note_hint') }}</small>
                        @error('note')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            {{-- Summary --}}
            <div class="col-lg-5">
                <div class="bg-white border rounded-4 p-4">
                    <h5 class="fw-bold mb-3">{{ __('site.order_summary') }}</h5>

                    @foreach ($items as $item)
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <img src="{{ $item->product->img_path }}" alt="{{ $item->product->trans_name }}"
                                 width="52" height="52" style="object-fit:cover;border-radius:.5rem;background:var(--ph-soft)">
                            <div class="flex-grow-1 min-w-0">
                                <div class="small fw-semibold text-truncate">{{ $item->product->trans_name }}</div>
                                <div class="small text-muted">{{ __('site.quantity') }}: {{ $item->quantity }}</div>
                                @if ($item->product->requires_prescription)
                                    <span class="badge rx-badge mt-1">
                                        <i class="fas fa-prescription"></i> {{ __('site.prescription_only') }}
                                    </span>
                                @endif
                            </div>
                            @if ($item->product->show_price)
                                <span class="small fw-bold text-ph">{{ number_format($item->total, 2) }}</span>
                            @endif
                        </div>
                    @endforeach

                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">{{ __('site.total') }}</span>
                        <span class="h5 mb-0 text-ph fw-bold">{{ number_format($total, 2) }}</span>
                    </div>
                    @unless ($prices_visible)
                        <p class="small text-muted mt-2 mb-0">
                            <i class="fas fa-circle-info"></i> {{ __('site.price_on_confirmation') }}
                        </p>
                    @endunless

                    <button type="submit" class="btn btn-ph w-100 rounded-pill py-2 fw-bold mt-3">
                        <i class="fas fa-check"></i> {{ __('site.confirm_order') }}
                    </button>
                    <a href="{{ route('medicines.index') }}" class="btn btn-link w-100 text-muted btn-sm mt-1">
                        {{ __('site.continue_shopping') }}
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
