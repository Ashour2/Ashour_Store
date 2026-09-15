@extends('admin.master')

@section('content')
<h1 class="h3 mb-4 text-gray-800">{{ __('admin.consultation_details') }}</h1>

<div class="card mb-4">
    <div class="card-body">
        <p><strong>{{ __('admin.name') }}:</strong> {{ $consultation->name }}</p>
        <p><strong>{{ __('admin.phone') }}:</strong> {{ $consultation->phone }}
            <a class="btn btn-sm btn-success ml-2" target="_blank" href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $consultation->phone) }}">
                <i class="fab fa-whatsapp"></i> {{ __('admin.reply_whatsapp') }}
            </a>
        </p>
        @if ($consultation->product_id)
            <p><strong>{{ __('admin.medicine') }}:</strong> {{ $consultation->product->trans_name }}</p>
        @endif
        <p><strong>{{ __('admin.date') }}:</strong> {{ $consultation->created_at->format('Y-m-d H:i') }}</p>
        <hr>
        <p><strong>{{ __('admin.message') }}:</strong></p>
        <p class="text-muted">{{ $consultation->message }}</p>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if (session()->has('msg'))
            <div class="alert alert-{{ session('type') }}">{{ session('msg') }}</div>
        @endif
        <form action="{{ route('admin.consultations.update', $consultation->id) }}" method="POST">
            @csrf
            @method('put')
            <div class="mb-3">
                <label><strong>{{ __('admin.pharmacist_reply') }}</strong></label>
                <textarea name="reply" rows="4" class="form-control @error('reply') is-invalid @enderror">{{ old('reply', $consultation->reply) }}</textarea>
                @error('reply')
                    <small class="invalid-feedback">{{ $message }}</small>
                @enderror
            </div>
            <button class="btn btn-primary">{{ __('admin.save_reply') }}</button>
            <a href="{{ route('admin.consultations.index') }}" class="btn btn-secondary">{{ __('admin.back') }}</a>
        </form>
    </div>
</div>
@endsection

@section('title', 'Consultation')
