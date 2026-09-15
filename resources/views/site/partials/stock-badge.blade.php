@if ($medicine->show_stock)
    @if ($medicine->in_stock)
        <span class="badge bg-success-subtle text-success border border-success-subtle">
            <i class="fas fa-circle-check"></i>
            {{ __('site.in_stock') }}
            @if ($medicine->stock_display === 'quantity')
                ({{ $medicine->quantity }})
            @endif
        </span>
    @else
        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
            <i class="fas fa-circle-xmark"></i> {{ __('site.out_of_stock') }}
        </span>
    @endif
@endif
