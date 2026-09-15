@extends('admin.master')

@section('content')
<h1 class="h3 mb-4 text-gray-800">{{ __('admin.customers') }}
    <span class="badge badge-success">{{ $members->total() }}</span>
</h1>

@if (session()->has('msg'))
<div class="alert alert-{{ session('type') }} alert-dismissible fade show" role="alert">
    {{ session('msg') }}
    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
</div>
@endif

<table class="table table-bordered table-hover">
    <tr class="bg-dark text-white">
        <th>#</th>
        <th>{{ __('admin.name') }}</th>
        <th>{{ __('admin.phone') }}</th>
        <th>{{ __('admin.email') }}</th>
        <th>{{ __('admin.date') }}</th>
        <th>{{ __('admin.actions') }}</th>
    </tr>
    @forelse ($members as $member)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $member->name }}</td>
        <td>{{ $member->phone }}</td>
        <td>{{ $member->email ?: '—' }}</td>
        <td>{{ $member->created_at->format('Y-m-d H:i') }}</td>
        <td>
            <a class="btn btn-sm btn-success" target="_blank" href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $member->phone) }}"><i class="fab fa-whatsapp"></i></a>
            <form class="d-inline" action="{{ route('admin.customers.destroy', $member->id) }}" method="POST">
                @csrf @method('delete')
                <button onclick="return confirm('Are you sure?!')" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
            </form>
        </td>
    </tr>
    @empty
    <tr><td colspan="6" class="text-center">{{ __('admin.no_data') }}</td></tr>
    @endforelse
</table>
{{ $members->links() }}
@endsection

@section('title', 'Customers')
