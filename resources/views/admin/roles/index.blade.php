@extends('admin.master')

@section('title', 'Roles')

@section('content')
<h1 class="h3 mb-1 text-gray-800">{{ __('admin.roles') }}</h1>
<p class="text-muted mb-4">{{ __('admin.roles_hint') }}</p>

@if (session()->has('msg'))
    <div class="alert alert-{{ session('type') }} alert-dismissible fade show">
        {{ session('msg') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
@endif

<div class="row">
    @foreach ($roles as $role)
        <div class="col-md-4 mb-3">
            <div class="card h-100 {{ $role->is_locked ? 'border-danger' : '' }}">
                <div class="card-header bg-white">
                    <strong>
                        <i class="fas fa-user-shield {{ $role->is_locked ? 'text-danger' : 'text-secondary' }}"></i>
                        {{ $role->trans_name }}
                    </strong>
                    @if ($role->is_locked)
                        <span class="badge badge-danger float-right"><i class="fas fa-lock"></i></span>
                    @endif
                </div>
                <div class="card-body">
                    <p class="mb-1">
                        <span class="text-muted">{{ __('admin.permissions') }}:</span>
                        <strong>{{ $role->permissions_count }}</strong> / {{ $total_permissions }}
                    </p>
                    <p class="mb-3">
                        <span class="text-muted">{{ __('admin.users') }}:</span>
                        <strong>{{ $role->users_count }}</strong>
                    </p>

                    @php $pct = $total_permissions ? round($role->permissions_count / $total_permissions * 100) : 0; @endphp
                    <div class="progress mb-3" style="height:6px">
                        <div class="progress-bar {{ $role->is_locked ? 'bg-danger' : 'bg-success' }}"
                             style="width: {{ $pct }}%"></div>
                    </div>

                    @if ($role->is_locked)
                        <p class="small text-muted mb-0">
                            <i class="fas fa-circle-info"></i> {{ __('admin.owner_always_full') }}
                        </p>
                    @else
                        @can('roles.edit')
                            <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-sliders-h"></i> {{ __('admin.edit_permissions') }}
                            </a>
                        @endcan
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection
