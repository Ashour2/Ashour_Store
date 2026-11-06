<div class="header-actions">
    <div class="dropdown header-language">
        <button class="header-action-button border-0 bg-transparent" data-toggle="dropdown" title="اللغات"><i class="fas fa-globe"></i><span>اللغة</span><i class="fas fa-chevron-down header-chevron"></i></button>
        <div class="dropdown-menu header-dropdown-menu">
            @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                <a class="dropdown-item" rel="alternate" hreflang="{{ $localeCode }}" href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">{{ $properties['native'] }}</a>
            @endforeach
        </div>
    </div>
    <a href="{{ route('home') }}" target="_blank" rel="noopener" class="header-action-button"><i class="fas fa-external-link-alt"></i><span>زيارة الموقع</span></a>
    @include('admin.notifications.menu', ['headerNotification' => true])
    <span class="header-welcome"><i class="fas fa-hand-wave"></i> مرحباً، {{ auth()->user()->name }}</span>
</div>