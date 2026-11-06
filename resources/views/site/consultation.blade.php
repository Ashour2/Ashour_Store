@extends('site.layout')

@section('title', __('site.consultation_title'))

@section('content')
@php $wa = preg_replace('/[^0-9]/', '', setting('whatsapp_number')); @endphp
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                     style="width:72px;height:72px;background:var(--ph-soft);color:var(--ph-primary-dark);font-size:32px;">
                    <i class="fas fa-user-md"></i>
                </div>
                <h3 class="fw-bold">{{ __('site.consultation_title') }}</h3>
                <p class="text-muted">{{ __('site.consultation_desc') }}</p>
            </div>

            @if (session('consultation_sent'))
                <div class="alert alert-success text-center">
                    <i class="fas fa-check-circle"></i> {{ __('site.consultation_sent') }}
                </div>
            @endif

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <form action="{{ route('consultation.store') }}" method="POST" id="consultForm" enctype="multipart/form-data">
                        @csrf
                        @if ($product)
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                        @endif

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('site.your_name') }}</label>
                                <input type="text" name="name" id="f_name" value="{{ old('name') }}"
                                       class="form-control @error('name') is-invalid @enderror" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('site.your_phone') }}</label>
                                <input type="text" name="phone" id="f_phone" value="{{ old('phone') }}"
                                       class="form-control @error('phone') is-invalid @enderror" required>
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            @if ($product)
                                <div class="col-12">
                                    <label class="form-label">{{ __('site.related_medicine') }}</label>
                                    <input type="text" class="form-control" value="{{ $product->trans_name }}" disabled>
                                </div>
                            @endif

                            <div class="col-12">
                                <label class="form-label"><i class="fas fa-clinic-medical text-ph"></i> اختر الصيدلية المناسبة للاستشارة <span class="text-muted small">(اختياري)</span></label>
                                <select name="pharmacy_id" class="form-select @error('pharmacy_id') is-invalid @enderror">
                                    <option value="">دع مدير المنصة يوجّه الاستشارة</option>
                                    @foreach ($pharmacies as $pharmacy)
                                        <option value="{{ $pharmacy->id }}" @selected(old('pharmacy_id') == $pharmacy->id)>{{ $pharmacy->trans_name }} — {{ $pharmacy->address ?: 'العنوان غير مسجل' }}</option>
                                    @endforeach
                                </select>
                                @error('pharmacy_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">{{ __('site.your_message') }}</label>
                                <textarea name="message" id="f_message" rows="4"
                                          class="form-control @error('message') is-invalid @enderror" required>{{ old('message') }}</textarea>
                                @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">
                                    <i class="fas fa-file-prescription text-ph"></i>
                                    {{ __('site.upload_prescription') }}
                                    <span class="text-muted small">({{ __('site.optional') }})</span>
                                </label>
                                <input type="file" name="prescription" accept="image/*,application/pdf"
                                       class="form-control @error('prescription') is-invalid @enderror">
                                @error('prescription')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <small class="text-muted">{{ __('site.prescription_hint') }}</small>
                            </div>

                            <div class="col-12 d-flex gap-2 flex-wrap">
                                <button type="submit" class="btn btn-ph btn-lg rounded-pill px-4">
                                    <i class="fas fa-paper-plane"></i> {{ __('site.send') }}
                                </button>
                                @if ($wa)
                                    <button type="button" id="waBtn" class="btn btn-success btn-lg rounded-pill px-4">
                                        <i class="fab fa-whatsapp"></i> {{ __('site.send_whatsapp') }}
                                    </button>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (function () {
        var waBtn = document.getElementById('waBtn');
        if (!waBtn) return;
        var product = @json($product?->trans_name);
        waBtn.addEventListener('click', function () {
            var name = document.getElementById('f_name').value;
            var phone = document.getElementById('f_phone').value;
            var msg = document.getElementById('f_message').value;
            var lines = [];
            if (name) lines.push({{ Js::from(__('site.your_name')) }} + ': ' + name);
            if (phone) lines.push({{ Js::from(__('site.your_phone')) }} + ': ' + phone);
            if (product) lines.push({{ Js::from(__('site.related_medicine')) }} + ': ' + product);
            if (msg) lines.push(msg);
            var text = encodeURIComponent(lines.join('\n'));
            window.open('https://wa.me/{{ $wa }}?text=' + text, '_blank');
        });
    })();
</script>
@endpush
@endsection
