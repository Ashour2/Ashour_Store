@extends('site.layout')

@section('title', __('site.health_articles'))

@section('content')
<div class="container py-5">
    <h3 class="fw-bold mb-1"><i class="fas fa-notes-medical text-ph"></i> {{ __('site.health_articles') }}</h3>
    <p class="text-muted mb-4">{{ __('site.health_articles_sub') }}</p>

    <div class="row g-4">
        @forelse ($articles as $article)
            <div class="col-md-4">
                <a href="{{ route('articles.show', $article->id) }}" class="med-card d-block h-100">
                    <img src="{{ $article->img_path }}" alt="{{ $article->trans_name }}">
                    <div class="p-3">
                        <h6 class="fw-bold text-dark mb-2">{{ $article->trans_name }}</h6>
                        <p class="small text-muted mb-2">{{ $article->excerpt }}</p>
                        <span class="text-ph fw-semibold small">{{ __('site.read_more') }}
                            <i class="fas fa-arrow-{{ LaravelLocalization::getCurrentLocaleDirection() == 'rtl' ? 'left' : 'right' }}"></i>
                        </span>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                <p class="text-muted">{{ __('site.no_articles') }}</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $articles->links() }}</div>
</div>
@endsection
