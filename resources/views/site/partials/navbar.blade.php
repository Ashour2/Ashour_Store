<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand d-inline-flex align-items-center gap-2" href="{{ route('home') }}">
            @if ($logo = logo_url())
                <img src="{{ $logo }}" alt="{{ tsetting('site_name') }}" class="brand-logo">
            @else
                <i class="fas fa-mortar-pestle"></i>
            @endif
            {{ tsetting('site_name') }}
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">{{ __('site.home') }}</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('medicines.index') }}">{{ __('site.medicines') }}</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('articles.index') }}">{{ __('site.health_articles') }}</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('consultation.create') }}">{{ __('site.consultation') }}</a></li>
            </ul>
            <div class="d-flex align-items-center gap-2">
                {{-- Language switch --}}
                @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                    @if ($localeCode != app()->getLocale())
                        <a class="btn btn-sm btn-outline-secondary rounded-pill"
                           href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
                            <i class="fas fa-globe"></i> {{ $properties['native'] }}
                        </a>
                    @endif
                @endforeach
                <a class="btn btn-sm btn-ph rounded-pill px-3" href="{{ route('consultation.create') }}">
                    <i class="fas fa-comment-medical"></i> {{ __('site.request_consultation') }}
                </a>
                <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#joinModal">
                    <i class="fas fa-user-plus"></i> {{ __('site.join_community') }}
                </button>
            </div>
        </div>
    </div>
</nav>
