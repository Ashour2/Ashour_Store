@extends('site.layout')

@section('title', __('site.medicines'))

@section('content')
<div class="container py-5">
    <h3 class="fw-bold mb-4">{{ __('site.medicines') }}</h3>

    {{-- Search + filter --}}
    <form action="{{ route('medicines.index') }}" method="GET" class="row g-2 mb-4">
        <div class="col-md-7">
            <input type="text" name="q" value="{{ $q }}" class="form-control form-control-lg"
                   placeholder="{{ __('site.search_placeholder') }}">
        </div>
        <div class="col-md-3">
            <select name="category" class="form-select form-select-lg">
                <option value="">{{ __('site.all_categories') }}</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ (string)$category_id === (string)$category->id ? 'selected' : '' }}>
                        {{ $category->trans_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-ph btn-lg w-100"><i class="fas fa-search"></i> {{ __('site.search') }}</button>
        </div>
    </form>

    {{-- Results --}}
    <div class="row g-3">
        @forelse ($medicines as $medicine)
            <div class="col-6 col-md-4 col-lg-3">
                @include('site.partials.medicine-card')
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <p class="text-muted">{{ __('site.no_medicines') }}</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $medicines->links() }}
    </div>
</div>
@endsection
