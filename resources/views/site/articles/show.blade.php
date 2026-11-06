@extends('site.layout')

@section('title', $article->trans_name)

@section('content')
<div class="container py-5">
    <nav class="mb-4 small">
        <a href="{{ route('home') }}" class="text-muted">{{ __('site.home') }}</a>
        <span class="mx-1">/</span>
        <a href="{{ route('articles.index') }}" class="text-muted">{{ __('site.health_articles') }}</a>
        <span class="mx-1">/</span>
        <span class="text-ph">{{ $article->trans_name }}</span>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="fw-bold mb-2">{{ $article->trans_name }}</h1>
            <p class="text-muted small mb-4"><i class="far fa-calendar"></i> {{ $article->created_at->format('Y-m-d') }}</p>

            @if ($article->image && $article->image->path && $article->image->path !== 'no-image.png')
                <img src="{{ $article->img_path }}" alt="{{ $article->trans_name }}" class="img-fluid rounded-4 mb-4 w-100" style="max-height:420px;object-fit:cover;">
            @endif

            <div class="article-body {{ $article->style_class }}">
                {!! nl2br(e($article->trans_description)) !!}
            </div>
        </div>
    </div>

    @if ($related->count())
        <hr class="my-5">
        <h4 class="fw-bold mb-4">{{ __('site.related_articles') }}</h4>
        <div class="row g-4">
            @foreach ($related as $article)
                <div class="col-md-4">
                    <a href="{{ route('articles.show', $article->id) }}" class="med-card d-block h-100">
                        <img src="{{ $article->img_path }}" alt="{{ $article->trans_name }}">
                        <div class="p-3">
                            <h6 class="fw-bold text-dark mb-2">{{ $article->trans_name }}</h6>
                            <p class="small text-muted mb-0">{{ $article->excerpt }}</p>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
