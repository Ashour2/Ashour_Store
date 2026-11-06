@csrf
<div class="form-row">
    <div class="form-group col-md-6">
        <label>{{ __('admin.pharmacy_name') }} (AR) <span class="text-danger">*</span></label>
        <input type="text" name="name_ar" value="{{ old('name_ar', $pharmacy->name_ar) }}"
               class="form-control @error('name_ar') is-invalid @enderror" required>
        @error('name_ar')<small class="invalid-feedback">{{ $message }}</small>@enderror
    </div>
    <div class="form-group col-md-6">
        <label>{{ __('admin.pharmacy_name') }} (EN) <span class="text-danger">*</span></label>
        <input type="text" name="name_en" value="{{ old('name_en', $pharmacy->name_en) }}"
               class="form-control @error('name_en') is-invalid @enderror" required>
        @error('name_en')<small class="invalid-feedback">{{ $message }}</small>@enderror
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-6">
        <label>{{ __('admin.phone') }}</label>
        <input type="text" name="phone" dir="ltr" value="{{ old('phone', $pharmacy->phone) }}"
               class="form-control @error('phone') is-invalid @enderror">
        @error('phone')<small class="invalid-feedback">{{ $message }}</small>@enderror
    </div>
    <div class="form-group col-md-6">
        <label>{{ __('admin.address') }}</label>
        <input type="text" name="address" value="{{ old('address', $pharmacy->address) }}"
               class="form-control @error('address') is-invalid @enderror">
        @error('address')<small class="invalid-feedback">{{ $message }}</small>@enderror
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-4"><label>خطة الاشتراك</label><input type="text" name="subscription_plan" value="{{ old('subscription_plan', $pharmacy->subscription_plan) }}" class="form-control" placeholder="مثال: شهري"></div>
    <div class="form-group col-md-4"><label>حالة الاشتراك</label><select name="subscription_status" class="form-control"><option value="active" @selected(old('subscription_status', $pharmacy->subscription_status ?: 'active') === 'active')>نشط</option><option value="trial" @selected(old('subscription_status', $pharmacy->subscription_status) === 'trial')>تجريبي</option><option value="suspended" @selected(old('subscription_status', $pharmacy->subscription_status) === 'suspended')>معلّق</option></select></div>
    <div class="form-group col-md-4"><label>ينتهي الاشتراك في</label><input type="date" name="subscription_ends_at" value="{{ old('subscription_ends_at', optional($pharmacy->subscription_ends_at)->format('Y-m-d')) }}" class="form-control"></div>
</div>

<div class="form-group">
    <div class="custom-control custom-switch">
        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1"
               {{ old('is_active', $pharmacy->exists ? $pharmacy->is_active : true) ? 'checked' : '' }}>
        <label class="custom-control-label" for="is_active">{{ __('admin.pharmacy_active') }}</label>
    </div>
</div>

<button class="btn btn-primary"><i class="fas fa-save"></i> {{ __('admin.save') }}</button>
<a href="{{ route('admin.pharmacies.index') }}" class="btn btn-secondary">{{ __('admin.back') }}</a>
