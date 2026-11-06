@csrf

<div class="form-group">
    <label for="user-warehouse">المستودع (لحسابات المستودعات فقط)</label>
    <select id="user-warehouse" name="warehouse_id" class="form-control @error('warehouse_id') is-invalid @enderror">
        <option value="">— اختر المستودع —</option>
        @foreach($warehouses as $warehouse)<option value="{{ $warehouse->id }}" @selected(old('warehouse_id', $user->warehouse_id) == $warehouse->id)>{{ $warehouse->name }}</option>@endforeach
    </select>
    @error('warehouse_id')<small class="invalid-feedback">{{ $message }}</small>@enderror
</div>

<div class="form-row">
    <div class="form-group col-md-6">
        <label>{{ __('admin.name') }} <span class="text-danger">*</span></label>
        <input type="text" name="name" value="{{ old('name', $user->name) }}"
               class="form-control @error('name') is-invalid @enderror" required>
        @error('name')<small class="invalid-feedback">{{ $message }}</small>@enderror
    </div>
    <div class="form-group col-md-6">
        <label>{{ __('admin.email') }} <span class="text-danger">*</span></label>
        <input type="email" name="email" value="{{ old('email', $user->email) }}" dir="ltr"
               class="form-control @error('email') is-invalid @enderror" required>
        <small class="text-muted">{{ __('admin.email_is_login') }}</small>
        @error('email')<small class="invalid-feedback">{{ $message }}</small>@enderror
    </div>
</div>

<div class="form-group">
    <label>{{ __('admin.role') }} <span class="text-danger">*</span></label>
    <select name="role_id" class="form-control @error('role_id') is-invalid @enderror" required>
        <option value="">— {{ __('admin.choose_role') }} —</option>
        @foreach ($roles as $role)
            <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                {{ $role->trans_name }}
            </option>
        @endforeach
    </select>
    <small class="text-muted">{{ __('admin.role_hint') }}</small>
    @error('role_id')<small class="invalid-feedback">{{ $message }}</small>@enderror
</div>

<div class="form-group">
    <label>{{ __('admin.pharmacy') }}</label>
    <select name="pharmacy_id" class="form-control @error('pharmacy_id') is-invalid @enderror">
        <option value="">— {{ __('admin.choose_pharmacy') }} —</option>
        @foreach ($pharmacies as $pharmacy)
            <option value="{{ $pharmacy->id }}" {{ old('pharmacy_id', $user->pharmacy_id) == $pharmacy->id ? 'selected' : '' }}>
                {{ $pharmacy->trans_name }}
            </option>
        @endforeach
    </select>
    <small class="text-muted">{{ __('admin.pharmacy_for_pharmacist_hint') }}</small>
    @error('pharmacy_id')<small class="invalid-feedback">{{ $message }}</small>@enderror
</div>

<div class="form-row">
    <div class="form-group col-md-6">
        <label>
            {{ __('admin.password') }}
            @if ($user->exists)
                <small class="text-muted">({{ __('admin.leave_blank_to_keep') }})</small>
            @else
                <span class="text-danger">*</span>
            @endif
        </label>
        <input type="password" name="password" autocomplete="new-password"
               class="form-control @error('password') is-invalid @enderror"
               @unless ($user->exists) required @endunless>
        @error('password')<small class="invalid-feedback">{{ $message }}</small>@enderror
    </div>
    <div class="form-group col-md-6">
        <label>{{ __('admin.confirm_password') }}</label>
        <input type="password" name="password_confirmation" autocomplete="new-password"
               class="form-control" @unless ($user->exists) required @endunless>
    </div>
</div>

<button class="btn btn-primary"><i class="fas fa-save"></i> {{ __('admin.save') }}</button>
<a href="{{ route('admin.users.index') }}" class="btn btn-secondary">{{ __('admin.back') }}</a>
