@extends('admin.master')

@section('content')
<h1 class="h3 mb-4 text-gray-800">
    {{ __('admin.consultations') }}
    @if ($new_count)
        <span class="badge badge-danger">{{ $new_count }} {{ __('admin.new') }}</span>
    @endif
</h1>

@if (session()->has('msg'))
<div class="alert alert-{{ session('type') }} alert-dismissible fade show" role="alert">
    {{ session('msg') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
</div>
@endif

<table class="table table-bordered table-hover">
    <tr class="bg-dark text-white">
        <th>#</th>
        <th>{{ __('admin.name') }}</th>
        <th>{{ __('admin.phone') }}</th>
        <th>{{ __('admin.medicine') }}</th>
        <th>{{ __('admin.prescription') }}</th>
        <th>{{ __('admin.status') }}</th>
        <th>{{ __('admin.date') }}</th>
        <th>{{ __('admin.actions') }}</th>
    </tr>
    @forelse ($consultations as $consultation)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $consultation->name }}</td>
        <td>{{ $consultation->phone }}</td>
        <td>{{ $consultation->product_id ? $consultation->product->trans_name : '—' }}</td>
        <td>
            @if ($consultation->prescription)
                <a href="{{ $consultation->prescription_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-file-prescription"></i>
                </a>
            @else
                <span class="text-muted">—</span>
            @endif
        </td>
        <td>
            @if ($consultation->status == 'new')
                <span class="badge badge-warning">{{ __('admin.new') }}</span>
            @else
                <span class="badge badge-success">{{ __('admin.answered') }}</span>
            @endif
        </td>
        <td>{{ $consultation->created_at->format('Y-m-d H:i') }}</td>
        <td>
            <a class="btn btn-sm btn-info" href="{{ route('admin.consultations.show', $consultation->id) }}"><i class="fas fa-eye"></i></a>
            <a class="btn btn-sm btn-success" target="_blank" href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $consultation->phone) }}"><i class="fab fa-whatsapp"></i></a>
            <form class="d-inline" action="{{ route('admin.consultations.destroy', $consultation->id) }}" method="POST">
                @csrf
                @method('delete')
                <button onclick="return confirm('Are you sure?!')" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
            </form>
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="8" class="text-center">{{ __('admin.no_data') }}</td>
    </tr>
    @endforelse
</table>
{{ $consultations->links() }}
@endsection

@section('title', 'Consultations')
