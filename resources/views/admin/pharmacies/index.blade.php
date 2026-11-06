@extends('admin.master')

@section('title', 'Pharmacies')

@section('content')
<h1 class="h3 mb-1 text-gray-800">{{ __('admin.pharmacies') }}</h1>
<p class="text-muted mb-4">
    <span class="badge badge-secondary"><i class="fas fa-lock"></i> {{ __('admin.stock_admin_only') }}</span>
</p>

@if (session()->has('msg'))
    <div class="alert alert-{{ session('type') }} alert-dismissible fade show">
        {{ session('msg') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
@endif

<a href="{{ route('admin.pharmacies.create') }}" class="btn btn-primary mb-3">
    <i class="fas fa-plus"></i> {{ __('admin.add_pharmacy') }}
</a>

<table class="table table-bordered table-hover bg-white">
    <tr class="bg-dark text-white">
        <th>#</th>
        <th>{{ __('admin.pharmacy_name') }}</th>
        <th>{{ __('admin.phone') }}</th>
        <th>{{ __('admin.address') }}</th>
        <th>{{ __('admin.medicines_count') }}</th>
        <th>{{ __('admin.status') }}</th>
        <th>{{ __('admin.actions') }}</th>
    </tr>
    @forelse ($pharmacies as $pharmacy)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>
                <a href="{{ route('admin.pharmacies.show', $pharmacy->id) }}">
                    <i class="fas fa-clinic-medical text-success"></i> {{ $pharmacy->trans_name }}
                </a>
            </td>
            <td>{!! $pharmacy->phone ? '<span dir="ltr">' . e($pharmacy->phone) . '</span>' : '—' !!}</td>
            <td>{{ $pharmacy->address ?: '—' }}</td>
            <td>
                <a href="{{ route('admin.pharmacies.show', $pharmacy->id) }}"
                   class="btn btn-sm {{ $pharmacy->products_count ? 'btn-info' : 'btn-outline-secondary' }}"
                   title="{{ __('admin.medicines_in_pharmacy') }}">
                    <i class="fas fa-capsules"></i> {{ $pharmacy->products_count }}
                </a>
            </td>
            <td>
                @if ($pharmacy->is_active)
                    <span class="badge badge-success">{{ __('admin.pharmacy_active') }}</span>
                @else
                    <span class="badge badge-secondary">{{ __('admin.inactive') }}</span>
                @endif
            </td>
            <td>
                <a class="btn btn-sm btn-primary" href="{{ route('admin.pharmacies.edit', $pharmacy->id) }}">
                    <i class="fas fa-edit"></i>
                </a>
                @if (auth()->user()->isSuperAdmin() && $pharmacy->users_count)
                    <form class="d-inline" action="{{ route('admin.pharmacies.impersonate', $pharmacy) }}" method="POST" onsubmit="return confirm('سيتم الدخول إلى حساب هذه الصيدلية. متابعة؟');">
                        @csrf <button class="btn btn-sm btn-warning" title="الدخول كصيدلي"><i class="fas fa-user-secret"></i></button>
                    </form>
                @endif
                @if ($pharmacy->wa_phone)
                    <a class="btn btn-sm btn-success" target="_blank" href="https://wa.me/{{ $pharmacy->wa_phone }}">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                @endif
                <form class="d-inline" action="{{ route('admin.pharmacies.destroy', $pharmacy->id) }}" method="POST">
                    @csrf
                    @method('delete')
                    <button onclick="return confirm('Are you sure?!')" class="btn btn-sm btn-danger">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="10" class="text-center">{{ __('admin.no_pharmacies') }}</td></tr>
    @endforelse
</table>
{{ $pharmacies->links() }}
@endsection
