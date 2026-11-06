<nav class="navbar navbar-expand-lg site-navbar sticky-top">
    <div class="container">
        <a class="navbar-brand site-brand d-inline-flex align-items-center gap-2" href="{{ route('home') }}">
            @if ($logo = logo_url())
                <img src="{{ $logo }}" alt="{{ tsetting('site_name') }}" class="brand-logo">
            @else
                <i class="fas fa-mortar-pestle"></i>
            @endif
            <span>{{ tsetting('site_name') }}</span>
        </a>
        <button class="navbar-toggler site-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav" aria-label="القائمة">
            <i class="fas fa-bars"></i>
        </button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav site-nav-links mx-auto">
                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">{{ __('site.home') }}</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('medicines.index') }}">{{ __('site.medicines') }}</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('articles.index') }}">{{ __('site.health_articles') }}</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('consultation.create') }}">{{ __('site.consultation') }}</a></li>
            </ul>
            <div class="site-nav-actions">
                @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                    @if ($localeCode != app()->getLocale())
                        <a class="nav-icon-button" title="{{ $properties['native'] }}" href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}"><i class="fas fa-globe"></i><span>{{ $properties['native'] }}</span></a>
                    @endif
                @endforeach
                <button type="button" class="nav-icon-button" data-bs-toggle="modal" data-bs-target="#joinModal"><i class="fas fa-user-plus"></i><span>انضمام إلى مجتمع لاين فارم</span></button>
                @guest
                    <a class="nav-login" href="{{ route('login') }}"><i class="fas fa-right-to-bracket"></i> دخول</a>
                    <a class="nav-pharmacist" href="{{ route('register', ['type' => 'pharmacist']) }}"><i class="fas fa-user-doctor"></i> تسجيل صيدلي</a>
                @endguest
                @auth
                    <a class="nav-login" href="{{ route('admin.index') }}"><i class="fas fa-gauge-high"></i> لوحة التحكم</a>
                @endauth
            </div>
        </div>
    </div>
</nav>
@if ($navCategories->isNotEmpty())
<div class="quick-category-bar">
    <div class="container"><div class="quick-category-inner">
        <span class="quick-category-title"><i class="fas fa-shapes"></i> التصنيفات</span>
        <div class="quick-category-links">
            @foreach ($navCategories->take(5) as $navCategory)
                <a href="{{ route('medicines.index', ['category' => $navCategory->id]) }}">{{ $navCategory->trans_name }}</a>
            @endforeach
            @if ($navCategories->count() > 5)
                <div class="dropdown">
                    <button class="quick-more-button" data-bs-toggle="dropdown">تصفح المزيد من التصنيفات <i class="fas fa-chevron-down"></i></button>
                    <div class="dropdown-menu quick-more-menu">
                        @foreach ($navCategories->skip(5) as $navCategory)
                            <a class="dropdown-item" href="{{ route('medicines.index', ['category' => $navCategory->id]) }}">{{ $navCategory->trans_name }}</a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div></div>
</div>
@endif