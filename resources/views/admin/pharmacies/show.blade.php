@extends('admin.master')

@section('title', 'Pharmacy medicines')

@section('content')
@php $branch_total = $pharmacy->products->sum('pivot.quantity'); @endphp

<h1 class="h3 mb-1 text-gray-800">
    <i class="fas fa-clinic-medical text-success"></i> {{ $pharmacy->trans_name }}
    @unless ($pharmacy->is_active)
        <span class="badge badge-secondary">{{ __('admin.inactive') }}</span>
    @endunless
</h1>
<p class="text-muted mb-4">
    {{ __('admin.medicines_in_pharmacy') }}
    <span class="badge badge-secondary ml-1"><i class="fas fa-lock"></i> {{ __('admin.stock_admin_only') }}</span>
</p>

<div class="row mb-3">
    <div class="col-md-4">
        <div class="info-box">
            <span class="info-box-icon bg-info"><i class="fas fa-capsules"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">{{ __('admin.medicines_count') }}</span>
                <span class="info-box-number">{{ $products->total() }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="info-box">
            <span class="info-box-icon bg-success"><i class="fas fa-boxes-stacked"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">{{ __('admin.total_units_here') }}</span>
                <span class="info-box-number">{{ $branch_total }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="info-box">
            <span class="info-box-icon bg-warning"><i class="fas fa-phone"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">{{ __('admin.phone') }}</span>
                <span class="info-box-number" style="font-size:1rem" dir="ltr">{{ $pharmacy->phone ?: '—' }}</span>
            </div>
        </div>
    </div>
</div>

<table class="table table-bordered table-hover bg-white">
    <tr class="bg-dark text-white">
        <th>#</th>
        <th>{{ __('admin.image') }}</th>
        <th>{{ __('admin.medicine_name') }}</th>
        <th>{{ __('admin.categories') }}</th>
        <th>{{ __('admin.price') }}</th>
        <th>{{ __('admin.quantity_here') }}</th>
        <th>{{ __('admin.actions') }}</th>
    </tr>
    @forelse ($products as $product)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td><img width="60" src="{{ $product->img_path }}" alt=""></td>
            <td>{{ $product->trans_name }}</td>
            <td>{{ $product->category->trans_name }}</td>
            <td>{{ number_format($product->price, 2) }}</td>
            <td><span class="badge badge-success" style="font-size:.95rem">{{ $product->pivot->quantity }}</span></td>
            <td>
                <a class="btn btn-sm btn-warning" title="{{ __('admin.stock_location') }}"
                   href="{{ route('admin.products.stock.edit', $product->id) }}">
                    <i class="fas fa-map-marker-alt"></i>
                </a>
                <a class="btn btn-sm btn-primary" href="{{ route('admin.products.edit', $product->id) }}">
                    <i class="fas fa-edit"></i>
                </a>
            </td>
        </tr>
    @empty
        <tr><td colspan="7" class="text-center">{{ __('admin.no_medicines_in_pharmacy') }}</td></tr>
    @endforelse
</table>
{{ $products->links() }}

<a href="{{ route('admin.pharmacies.index') }}" class="btn btn-secondary">{{ __('admin.back') }}</a>
<a href="{{ route('admin.pharmacies.edit', $pharmacy->id) }}" class="btn btn-primary">
    <i class="fas fa-edit"></i> {{ __('admin.edit_pharmacy') }}
</a>
@endsection
