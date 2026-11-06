@extends('admin.master')

@section('title', 'Stock locations')

@section('content')
@php
    $branch_total = $held->sum();
    $active_branches = $pharmacies->where('is_active', true);
@endphp

<h1 class="h3 mb-1 text-gray-800">
    <i class="fas fa-map-marker-alt text-danger"></i>
    {{ __('admin.stock_location') }}
</h1>
<p class="text-muted mb-4">
    <strong>{{ $product->trans_name }}</strong>
    <span class="badge badge-secondary ml-1"><i class="fas fa-lock"></i> {{ __('admin.stock_admin_only') }}</span>
</p>

@if (session()->has('msg'))
    <div class="alert alert-{{ session('type') }} alert-dismissible fade show">
        {{ session('msg') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
@endif

@if ($pharmacies->isEmpty())
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-clinic-medical fa-3x text-muted mb-3"></i>
            <h5>{{ __('admin.no_pharmacies') }}</h5>
            <p class="text-muted">{{ __('admin.add_pharmacy_first') }}</p>
            <a href="{{ route('admin.pharmacies.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> {{ __('admin.add_pharmacy') }}
            </a>
        </div>
    </div>
@else
<div class="row">
    <div class="col-lg-8">
        <form action="{{ route('admin.products.stock.update', $product->id) }}" method="POST">
            @csrf
            @method('put')
            <div class="card">
                <div class="card-header bg-white">
                    <strong>{{ __('admin.stock_locations') }}</strong>
                    <span class="float-right text-muted small">{{ __('admin.stock_hint') }}</span>
                </div>
                <table class="table table-bordered mb-0">
                    <tr class="bg-light">
                        <th style="width:45%">{{ __('admin.pharmacy_name') }}</th>
                        <th>{{ __('admin.phone') }}</th>
                        <th style="width:150px">{{ __('admin.quantity') }}</th><th style="width:170px">إتاحة للصيدلية</th>
                    </tr>
                    @foreach ($pharmacies as $pharmacy)
                        @php $quantity = old('quantities.' . $pharmacy->id, $held[$pharmacy->id] ?? null); @endphp
                        <tr class="{{ $pharmacy->is_active ? '' : 'text-muted bg-light' }}">
                            <td>
                                <i class="fas fa-clinic-medical {{ $pharmacy->is_active ? 'text-success' : 'text-secondary' }}"></i>
                                {{ $pharmacy->trans_name }}
                                @unless ($pharmacy->is_active)
                                    <span class="badge badge-secondary">{{ __('admin.inactive') }}</span>
                                @endunless
                                @if ($pharmacy->address)
                                    <div class="small text-muted">{{ $pharmacy->address }}</div>
                                @endif
                            </td>
                            <td>
                                @if ($pharmacy->phone)
                                    <span dir="ltr">{{ $pharmacy->phone }}</span>
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                <input type="number" min="0" max="1000000"
                                       name="quantities[{{ $pharmacy->id }}]"
                                       value="{{ $quantity }}"
                                       class="form-control stock-input @error('quantities.' . $pharmacy->id) is-invalid @enderror"
                                       placeholder="0">
                                @error('quantities.' . $pharmacy->id)
                                    <small class="invalid-feedback">{{ $message }}</small>
                                @enderror
                            </td>
                            <td>
                                @php $isBlocked = in_array($pharmacy->id, old('blocked', $blocked->all())); @endphp
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input access-toggle" type="checkbox" id="blocked{{ $pharmacy->id }}" name="blocked[]" value="{{ $pharmacy->id }}" @checked($isBlocked)>
                                    <label class="custom-control-label" for="blocked{{ $pharmacy->id }}">منع هذا الدواء</label>
                                </div>
                                <small class="text-muted">غير المحدد: ظاهر افتراضياً</small>
                            </td>
                        </tr>
                    @endforeach
                    <tr class="font-weight-bold">
                        <td colspan="3" class="text-right">{{ __('admin.branch_total') }}</td>
                        <td id="stockTotal">{{ $branch_total }}</td>
                    </tr>
                </table>
                <div class="card-footer bg-white">
                    <button class="btn btn-primary"><i class="fas fa-save"></i> {{ __('admin.save') }}</button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">{{ __('admin.back') }}</a>
                </div>
            </div>
        </form>
    </div>

    {{-- Summary --}}
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-white"><strong>{{ $product->trans_name }}</strong></div>
            <div class="card-body">
                <img src="{{ $product->img_path }}" alt="" class="img-fluid rounded mb-3" style="max-height:160px;object-fit:contain;width:100%">

                <p class="mb-1">
                    <span class="text-muted">{{ __('admin.catalog_quantity') }}:</span>
                    <strong>{{ $product->quantity }}</strong>
                </p>
                <p class="mb-1">
                    <span class="text-muted">{{ __('admin.branch_total') }}:</span>
                    <strong>{{ $branch_total }}</strong>
                </p>
                @if ($branch_total != (int) $product->quantity)
                    <div class="alert alert-warning small mt-2 mb-0">
                        <i class="fas fa-triangle-exclamation"></i> {{ __('admin.stock_mismatch') }}
                    </div>
                @endif

                <hr>
                <p class="mb-1 text-muted">{{ __('admin.available_at') }}:</p>
                @forelse ($held->filter() as $pharmacy_id => $quantity)
                    <div class="d-flex justify-content-between border-bottom py-1">
                        <span>{{ $pharmacies->firstWhere('id', $pharmacy_id)?->trans_name }}</span>
                        <strong>{{ $quantity }}</strong>
                    </div>
                @empty
                    <p class="text-muted small">{{ __('admin.not_stocked') }}</p>
                @endforelse

                <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-outline-primary btn-block mt-3">
                    <i class="fas fa-edit"></i> {{ __('admin.edit') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script>
    // Keep the running total in step with what the manager is typing
    (function () {
        const inputs = document.querySelectorAll('.stock-input');
        const total = document.getElementById('stockTotal');
        if (!total) return;

        function recalc() {
            let sum = 0;
            inputs.forEach(i => { sum += Math.max(0, parseInt(i.value) || 0); });
            total.textContent = sum;
        }

        inputs.forEach(i => i.addEventListener('input', recalc));
    })();
</script>
@endsection
