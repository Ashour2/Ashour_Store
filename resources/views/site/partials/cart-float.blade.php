@php
    $rtl = LaravelLocalization::getCurrentLocaleDirection() == 'rtl';
    $cart_count = cart()->count();
@endphp

{{-- Floating cart launcher: mirrors the WhatsApp bubble on the opposite side --}}
<button type="button" class="cart-float" id="cartFloat"
        data-bs-toggle="offcanvas" data-bs-target="#cartPanel" title="{{ __('site.my_cart') }}">
    <i class="fas fa-basket-shopping"></i>
    <span class="cart-float-badge {{ $cart_count ? '' : 'd-none' }}" id="cartCount">{{ $cart_count }}</span>
</button>

{{-- Slide-out cart panel --}}
<div class="offcanvas offcanvas-start cart-panel" tabindex="-1" id="cartPanel" aria-labelledby="cartPanelLabel">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title fw-bold" id="cartPanelLabel">
            <i class="fas fa-basket-shopping text-ph"></i> {{ __('site.my_cart') }}
            <span class="badge bg-ph-soft text-ph ms-1" id="cartLinesBadge">{{ $cart_count }}</span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-0 d-flex flex-column">
        {{-- Empty state --}}
        <div class="text-center p-4 my-auto {{ $cart_count ? 'd-none' : '' }}" id="cartEmpty">
            <div class="cart-empty-icon mb-3"><i class="fas fa-basket-shopping"></i></div>
            <h6 class="fw-bold">{{ __('site.cart_empty') }}</h6>
            <p class="text-muted small mb-3">{{ __('site.cart_empty_hint') }}</p>
            <a href="{{ route('medicines.index') }}" class="btn btn-ph rounded-pill px-4">
                {{ __('site.browse_medicines') }}
            </a>
        </div>

        {{-- Lines (rendered by JS from the cart JSON) --}}
        <div class="flex-grow-1 overflow-auto" id="cartItems"></div>

        {{-- Footer: total + checkout --}}
        <div class="border-top p-3 {{ $cart_count ? '' : 'd-none' }}" id="cartFooter">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted">{{ __('site.total') }}</span>
                <span class="fw-bold h5 mb-0 text-ph" id="cartTotal">0.00</span>
            </div>
            <p class="small text-muted d-none mb-2" id="cartPriceNote">
                <i class="fas fa-circle-info"></i> {{ __('site.price_on_confirmation') }}
            </p>
            <a href="{{ route('cart.checkout') }}" class="btn btn-ph w-100 rounded-pill py-2 fw-bold">
                <i class="fas fa-clipboard-check"></i> {{ __('site.checkout') }}
            </a>
            <button type="button" class="btn btn-link btn-sm text-danger w-100 mt-1" id="cartClear">
                <i class="fas fa-trash-can"></i> {{ __('site.clear_cart') }}
            </button>
        </div>
    </div>
</div>

{{-- Toast feedback for add / update / remove --}}
<div class="cart-toast" id="cartToast"><span id="cartToastText"></span></div>
