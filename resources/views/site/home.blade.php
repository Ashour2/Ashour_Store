@extends('site.layout')

@section('content')
<section class="hero home-hero">
    @include('site.partials.hero-slides')
    <div class="hero-shape hero-shape-one"></div><div class="hero-shape hero-shape-two"></div>
    <div class="container hero-inner">
        <div class="hero-panel text-center">
            <span class="hero-eyebrow"><i class="fas fa-heart-pulse"></i> رعاية صحية أقرب إليك</span>
            <h1>{{ tsetting('hero_title') ?: 'كل ما تحتاجه لصحتك في مكان واحد' }}</h1>
            <p>{{ tsetting('hero_subtitle') ?: 'ابحث عن الدواء، اختر الصيدلية المناسبة، واطلب بثقة.' }}</p>
            <form action="{{ route('medicines.index') }}" method="GET" class="search-box mx-auto">
                <div class="input-group"><span class="input-group-text"><i class="fas fa-search"></i></span><input type="text" name="q" class="form-control" placeholder="{{ __('site.search_placeholder') }}"><button class="btn" type="submit">{{ __('site.search') }}</button></div>
            </form>
            <div class="hero-actions"><a href="{{ route('medicines.index') }}" class="btn hero-primary">{{ __('site.browse_medicines') }} <i class="fas fa-arrow-left"></i></a><a href="{{ route('consultation.create') }}" class="btn hero-secondary"><i class="fas fa-comment-medical"></i> {{ __('site.request_consultation') }}</a></div>
        </div>
    </div>
</section>

@if ($mixedMedicines->isNotEmpty() || $categoryWidgets->isNotEmpty())
<section class="container medicine-gallery-zone">
    @if ($mixedMedicines->isNotEmpty())
        <section class="category-medicine-widget">
            <div class="gallery-widget-head">
                <div class="gallery-category-title"><span class="gallery-category-image gallery-random-icon"><i class="fas fa-sparkles"></i></span><div><span>اختيارات متنوعة</span><h2>أدوية مقترحة لك</h2></div></div>
                <a href="{{ route('medicines.index') }}">عرض كل الأدوية <i class="fas fa-arrow-left"></i></a>
            </div>
            <div class="row g-4 medicine-gallery-grid">
                @foreach ($mixedMedicines as $medicine)
                    <div class="col-6 col-lg">@include('site.partials.medicine-card')</div>
                @endforeach
            </div>
        </section>
    @endif
    @foreach ($categoryWidgets as $category)
        <section class="category-medicine-widget">
            <div class="gallery-widget-head">
                <div class="gallery-category-title">@if ($category->img_path)<span class="gallery-category-image"><img src="{{ $category->img_path }}" alt="{{ $category->trans_name }}"></span>@endif<div><span>معرض أدوية</span><h2>{{ $category->trans_name }}</h2></div></div>
                <a href="{{ route('medicines.index', ['category' => $category->id]) }}">عرض كل الأدوية <i class="fas fa-arrow-left"></i></a>
            </div>
            <div class="row g-4 medicine-gallery-grid">
                @foreach ($category->galleryMedicines as $medicine)
                    <div class="col-6 col-lg">@include('site.partials.medicine-card')</div>
                @endforeach
            </div>
        </section>
    @endforeach
</section>
@endif
@if ($articles->count())
<section class="article-zone"><div class="container home-section">
    <div class="section-heading"><div><span>معلومة موثوقة</span><h2><i class="fas fa-notes-medical"></i> {{ __('site.health_articles') }}</h2><p>{{ __('site.health_articles_sub') }}</p></div><a href="{{ route('articles.index') }}">{{ __('site.all_articles') }} <i class="fas fa-arrow-left"></i></a></div>
    <div class="row g-4">
        @foreach ($articles as $article)
            <div class="col-md-4"><a href="{{ route('articles.show', $article->id) }}" class="article-card d-block h-100"><img src="{{ $article->img_path }}" alt="{{ $article->trans_name }}"><div><span class="article-label">مقال صحي</span><h3>{{ $article->trans_name }}</h3><p>{{ $article->excerpt }}</p><b>{{ __('site.read_more') }} <i class="fas fa-arrow-left"></i></b></div></a></div>
        @endforeach
    </div>
</div></section>
@endif
@endsection
