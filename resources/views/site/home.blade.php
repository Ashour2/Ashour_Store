@extends('site.layout')

@section('content')
{{-- Hero --}}
<section class="hero py-5">
    @include('site.partials.hero-slides')

    <div class="container py-4 text-center">
        <h1 class="fw-bold mb-3">{{ tsetting('hero_title') }}</h1>
        <p class="lead mb-4 opacity-75">{{ tsetting('hero_subtitle') }}</p>

        <form action="{{ route('medicines.index') }}" method="GET" class="search-box mx-auto" style="max-width: 620px;">
            <div class="input-group shadow">
                <input type="text" name="q" class="form-control border-0 px-4" placeholder="{{ __('site.search_placeholder') }}">
                <button class="btn btn-light px-4 text-ph fw-bold" type="submit">
                    <i class="fas fa-search"></i> {{ __('site.search') }}
                </button>
            </div>
        </form>

        <div class="mt-4 d-flex gap-2 justify-content-center flex-wrap">
            <a href="{{ route('medicines.index') }}" class="btn btn-light rounded-pill px-4">{{ __('site.browse_medicines') }}</a>
            <a href="{{ route('consultation.create') }}" class="btn btn-outline-light rounded-pill px-4">
                <i class="fas fa-comment-medical"></i> {{ __('site.request_consultation') }}
            </a>
        </div>
    </div>
</section>

{{-- Categories --}}
@if ($categories->count())
<section class="container py-5">
    <h4 class="fw-bold mb-4">{{ __('site.categories') }}</h4>
    <div class="category-grid">
        @foreach ($categories as $category)
            <a href="{{ route('medicines.index', ['category' => $category->id]) }}" class="cat-pill">
                @if ($category->icon_class)
                    <span class="cat-icon"><i class="{{ $category->icon_class }}"></i></span>
                @endif
                <span class="cat-name">{{ $category->trans_name }}</span>
                <span class="badge bg-light text-muted">{{ $category->products_count }}</span>
            </a>
        @endforeach
    </div>
</section>
@endif

{{-- Featured medicines --}}
<section class="container pb-5">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="fw-bold mb-0">{{ __('site.featured_medicines') }}</h4>
        <a href="{{ route('medicines.index') }}" class="text-ph fw-semibold">{{ __('site.browse_medicines') }} <i class="fas fa-arrow-{{ LaravelLocalization::getCurrentLocaleDirection() == 'rtl' ? 'left' : 'right' }}"></i></a>
    </div>
    <div class="row g-3">
        @forelse ($medicines as $medicine)
            <div class="col-6 col-md-4 col-lg-3">
                @include('site.partials.medicine-card')
            </div>
        @empty
            <p class="text-muted">{{ __('site.no_medicines') }}</p>
        @endforelse
    </div>
</section>

{{-- Health articles --}}
@if ($articles->count())
<section style="background:#f0fdfa;">
    <div class="container py-5">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="fw-bold mb-1"><i class="fas fa-notes-medical text-ph"></i> {{ __('site.health_articles') }}</h4>
                <p class="text-muted mb-0">{{ __('site.health_articles_sub') }}</p>
            </div>
            <a href="{{ route('articles.index') }}" class="text-ph fw-semibold">
                {{ __('site.all_articles') }}
                <i class="fas fa-arrow-{{ LaravelLocalization::getCurrentLocaleDirection() == 'rtl' ? 'left' : 'right' }}"></i>
            </a>
        </div>
        <div class="row g-4">
            @foreach ($articles as $article)
                <div class="col-md-4">
                    <a href="{{ route('articles.show', $article->id) }}" class="med-card d-block h-100">
                        <img src="{{ $article->img_path }}" alt="{{ $article->trans_name }}">
                        <div class="p-3">
                            <h6 class="fw-bold text-dark mb-2">{{ $article->trans_name }}</h6>
                            <p class="small text-muted mb-2">{{ $article->excerpt }}</p>
                            <span class="text-ph fw-semibold small">
                                {{ __('site.read_more') }}
                                <i class="fas fa-arrow-{{ LaravelLocalization::getCurrentLocaleDirection() == 'rtl' ? 'left' : 'right' }}"></i>
                            </span>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
