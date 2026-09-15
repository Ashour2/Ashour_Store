@extends('admin.master')

@section('title', 'Role permissions')

@section('content')
<h1 class="h3 mb-1 text-gray-800">
    <i class="fas fa-user-shield"></i> {{ __('admin.edit_permissions') }}
    <small class="text-muted">— {{ $role->trans_name }}</small>
</h1>
<p class="text-muted mb-4">{{ __('admin.permissions_hint') }}</p>

<form action="{{ route('admin.roles.update', $role->id) }}" method="POST">
    @csrf
    @method('put')

    <div class="mb-3">
        <button type="button" class="btn btn-sm btn-outline-success" id="checkAll">
            <i class="fas fa-check-double"></i> {{ __('admin.select_all') }}
        </button>
        <button type="button" class="btn btn-sm btn-outline-secondary" id="uncheckAll">
            <i class="fas fa-xmark"></i> {{ __('admin.clear_all') }}
        </button>
        <span class="badge badge-info ml-2" id="permCount">0</span>
    </div>

    <div class="row">
        @foreach ($groups as $group => $permissions)
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card h-100">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <strong>{{ __('admin.group_' . $group) }}</strong>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input group-check"
                                   id="group{{ $loop->index }}" data-group="{{ $group }}">
                            <label class="custom-control-label small text-muted" for="group{{ $loop->index }}">
                                {{ __('admin.all') }}
                            </label>
                        </div>
                    </div>
                    <div class="card-body py-2">
                        @foreach ($permissions as $permission)
                            <div class="custom-control custom-checkbox mb-2">
                                <input type="checkbox" class="custom-control-input perm-check"
                                       data-group="{{ $group }}"
                                       id="perm{{ $permission->id }}"
                                       name="permissions[]" value="{{ $permission->id }}"
                                       {{ in_array($permission->id, $held) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="perm{{ $permission->id }}">
                                    {{ $permission->trans_name }}
                                    <code class="small text-muted d-block">{{ $permission->code }}</code>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <button class="btn btn-primary btn-lg"><i class="fas fa-save"></i> {{ __('admin.save') }}</button>
    <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary btn-lg">{{ __('admin.back') }}</a>
</form>
@endsection

@section('scripts')
<script>
// The layout loads jQuery after the content, so wire up on DOMContentLoaded
document.addEventListener('DOMContentLoaded', function () {
    const boxes = Array.from(document.querySelectorAll('.perm-check'));
    const groups = Array.from(document.querySelectorAll('.group-check'));
    const counter = document.getElementById('permCount');

    function inGroup(group) {
        return boxes.filter(function (box) { return box.dataset.group === group; });
    }

    /** Keep the per-group box in step: ticked, clear, or partly ticked. */
    function refresh() {
        groups.forEach(function (groupBox) {
            const members = inGroup(groupBox.dataset.group);
            const picked = members.filter(function (box) { return box.checked; }).length;

            groupBox.checked = picked === members.length && members.length > 0;
            groupBox.indeterminate = picked > 0 && picked < members.length;
        });

        counter.textContent = boxes.filter(function (box) { return box.checked; }).length;
    }

    groups.forEach(function (groupBox) {
        groupBox.addEventListener('change', function () {
            inGroup(groupBox.dataset.group).forEach(function (box) { box.checked = groupBox.checked; });
            refresh();
        });
    });

    boxes.forEach(function (box) { box.addEventListener('change', refresh); });

    document.getElementById('checkAll').addEventListener('click', function () {
        boxes.forEach(function (box) { box.checked = true; });
        refresh();
    });
    document.getElementById('uncheckAll').addEventListener('click', function () {
        boxes.forEach(function (box) { box.checked = false; });
        refresh();
    });

    refresh();
});
</script>
@endsection
