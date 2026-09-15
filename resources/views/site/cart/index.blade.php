@extends('site.layout')

@section('title', __('site.my_cart'))

@section('content')
<div class="container py-5">
    <h2 class="fw-bold mb-4"><i class="fas fa-basket-shopping text-ph"></i> {{ __('site.my_cart') }}</h2>

    @if ($items->isEmpty())
        <div class="bg-white border rounded-4 p-5 text-center">
            <div class="cart-empty-icon mb-3"><i class="fas fa-basket-shopping"></i></div>
            <h5 class="fw-bold">{{ __('site.cart_empty') }}</h5>
            <p class="text-muted">{{ __('site.cart_empty_hint') }}</p>
            <a href="{{ route('medicines.index') }}" class="btn btn-ph rounded-pill px-4">{{ __('site.browse_medicines') }}</a>
        </div>
    @else
        <div class="bg-white border rounded-4 overflow-hidden">
            @foreach ($items as $item)
                <div class="d-flex align-items-center gap-3 p-3 border-bottom">
                    <img src="{{ $item->product->img_path }}" alt="{{ $item->product->trans_name }}"
                         width="64" height="64" style="object-fit:cover;border-radius:.6rem;background:var(--ph-soft)">
                    <div class="flex-grow-1 min-w-0">
                        <a href="{{ route('medicines.show', $item->product->id) }}" class="fw-semibold text-dark text-truncate d-block">
                            {{ $item->product->trans_name }}
                        </a>
                        <span class="small text-muted">{{ __('site.quantity') }}: {{ $item->quantity }}</span>
                    </div>
                    @if ($item->product->show_price)
                        <span class="fw-bold text-ph">{{ number_format($item->total, 2) }}</span>
                    @endif
                    <form action="{{ route('cart.remove', $item->product->id) }}" method="POST">
                        @csrf
                        <button class="btn btn-sm text-danger" title="{{ __('site.remove') }}"><i class="fas fa-trash-can"></i></button>
                    </form>
                </div>
            @endforeach

            <div class="p-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span class="h5 mb-0">{{ __('site.total') }}: <span class="text-ph fw-bold">{{ number_format(cart()->total(), 2) }}</span></span>
                <a href="{{ route('cart.checkout') }}" class="btn btn-ph rounded-pill px-4">
                    <i class="fas fa-clipboard-check"></i> {{ __('site.checkout') }}
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
