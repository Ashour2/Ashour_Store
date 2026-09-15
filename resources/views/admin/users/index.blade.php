@extends('admin.master')

@section('title', 'Users')

@section('content')
<h1 class="h3 mb-1 text-gray-800">{{ __('admin.users') }}</h1>
<p class="text-muted mb-4">{{ __('admin.users_hint') }}</p>

@if (session()->has('msg'))
    <div class="alert alert-{{ session('type') }} alert-dismissible fade show">
        {{ session('msg') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
@endif

@can('users.create')
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary mb-3">
        <i class="fas fa-user-plus"></i> {{ __('admin.add_user') }}
    </a>
@endcan
@can('roles.view')
    <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary mb-3">
        <i class="fas fa-user-shield"></i> {{ __('admin.roles') }}
    </a>
@endcan

<table class="table table-bordered table-hover bg-white">
    <tr class="bg-dark text-white">
        <th>#</th>
        <th>{{ __('admin.name') }}</th>
        <th>{{ __('admin.email') }}</th>
        <th>{{ __('admin.role') }}</th>
        <th>{{ __('admin.date') }}</th>
        <th>{{ __('admin.actions') }}</th>
    </tr>
    @forelse ($users as $user)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>
                {{ $user->name }}
                @if ($user->id === auth()->id())
                    <span class="badge badge-info">{{ __('admin.you') }}</span>
                @endif
            </td>
            <td><span dir="ltr">{{ $user->email }}</span></td>
            <td>
                <span class="badge badge-{{ $user->role->is_super_admin ? 'danger' : 'secondary' }}">
                    {{ $user->role->trans_name }}
                </span>
            </td>
            <td>{{ $user->created_at?->format('Y-m-d') }}</td>
            <td>
                @if ($user->role->is_super_admin)
                    {{-- The owner is managed from their own account page --}}
                    <span class="text-muted small"><i class="fas fa-lock"></i> {{ __('admin.owner_protected') }}</span>
                @else
                    @can('users.edit')
                        <a class="btn btn-sm btn-primary" href="{{ route('admin.users.edit', $user->id) }}">
                            <i class="fas fa-edit"></i>
                        </a>
                    @endcan
                    @can('users.delete')
                        <form class="d-inline" action="{{ route('admin.users.destroy', $user->id) }}" method="POST">
                            @csrf
                            @method('delete')
                            <button onclick="return confirm('Are you sure?!')" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    @endcan
                @endif
            </td>
        </tr>
    @empty
        <tr><td colspan="6" class="text-center">{{ __('admin.no_data') }}</td></tr>
    @endforelse
</table>
{{ $users->links() }}
@endsection
