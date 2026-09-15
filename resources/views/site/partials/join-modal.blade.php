@php
    $rtl = LaravelLocalization::getCurrentLocaleDirection() == 'rtl';
    $codes = ['+966' => 'SA', '+971' => 'AE', '+965' => 'KW', '+974' => 'QA', '+973' => 'BH', '+968' => 'OM',
              '+962' => 'JO', '+970' => 'PS', '+972' => 'PS', '+20' => 'EG', '+964' => 'IQ', '+963' => 'SY', '+961' => 'LB',
              '+218' => 'LY', '+216' => 'TN', '+213' => 'DZ', '+212' => 'MA', '+90' => 'TR', '+1' => 'US', '+44' => 'GB'];
    $oldCode = old('country_code', setting('default_country_code', '+966'));
@endphp

<div class="modal fade" id="joinModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-user-plus text-ph"></i> {{ __('site.join_community') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('members.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small">{{ __('site.join_desc') }}</p>

                    <div class="mb-3">
                        <label class="form-label">{{ __('site.your_name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               class="form-control @error('name', 'join') is-invalid @enderror" required>
                        @error('name', 'join')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('site.whatsapp_number') }} <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <select name="country_code" class="form-select @error('country_code', 'join') is-invalid @enderror" style="max-width:120px;">
                                @foreach ($codes as $code => $iso)
                                    <option value="{{ $code }}" {{ $oldCode == $code ? 'selected' : '' }}>{{ $iso }} {{ $code }}</option>
                                @endforeach
                            </select>
                            <input type="tel" name="phone" value="{{ old('phone') }}" dir="ltr"
                                   class="form-control @error('phone', 'join') is-invalid @enderror"
                                   placeholder="5xxxxxxxx" required>
                        </div>
                        @error('phone', 'join')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        <small class="text-muted">{{ __('site.whatsapp_hint_public') }}</small>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">{{ __('site.email') }} <span class="text-muted small">({{ __('site.optional') }})</span></label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               class="form-control @error('email', 'join') is-invalid @enderror">
                        @error('email', 'join')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-ph rounded-pill px-4">
                        <i class="fas fa-check"></i> {{ __('site.join_now') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@if (session('member_joined'))
    <div class="position-fixed top-0 start-50 translate-middle-x mt-3 alert alert-success shadow d-flex align-items-center gap-2"
         style="z-index:2000;" id="joinToast">
        <i class="fas fa-circle-check"></i> {{ __('site.join_success') }}
    </div>
    <script>
        setTimeout(function () {
            var t = document.getElementById('joinToast');
            if (t) t.style.display = 'none';
        }, 5000);
    </script>
@endif

@if (session('open_join') || $errors->join->any())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var m = new bootstrap.Modal(document.getElementById('joinModal'));
            m.show();
        });
    </script>
@endif
