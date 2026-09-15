@php $can_order = ! ($medicine->show_stock && ! $medicine->in_stock); @endphp
<div class="med-card">
    <a href="{{ route('medicines.show', $medicine->id) }}">
        <img src="{{ $medicine->img_path }}" alt="{{ $medicine->trans_name }}">
    </a>
    <div class="p-3">
        @if ($medicine->category_id)
            <span class="badge bg-light text-ph mb-2">{{ $medicine->category->trans_name }}</span>
        @endif
        @if ($medicine->requires_prescription)
            <span class="badge rx-badge mb-2"><i class="fas fa-prescription"></i> {{ __('site.prescription_only') }}</span>
        @endif
        <h6 class="mb-1">
            <a class="text-ink text-dark" href="{{ route('medicines.show', $medicine->id) }}">{{ $medicine->trans_name }}</a>
        </h6>
        @if ($medicine->active_ingredient)
            <p class="small text-muted mb-2">{{ $medicine->active_ingredient }}</p>
        @endif
        <div class="mb-2">@include('site.partials.stock-badge')</div>
        <div class="d-flex align-items-center justify-content-between">
            @if ($medicine->show_price)
                <span class="fw-bold text-ph">{{ number_format($medicine->price, 2) }}</span>
            @else
                <span></span>
            @endif
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('medicines.show', $medicine->id) }}" class="btn btn-sm btn-ph rounded-pill">
                    {{ __('site.view_details') }}
                </a>
                <button type="button" class="btn-cart-add"
                        data-cart-add="{{ $medicine->id }}"
                        title="{{ $can_order ? __('site.add_to_cart') : __('site.cart_out_of_stock') }}"
                        @disabled(! $can_order)>
                    <i class="fas fa-cart-plus"></i>
                </button>
            </div>
        </div>
    </div>
</div>
