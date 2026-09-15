<footer class="pt-5 pb-4 mt-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                @if ($logo = logo_url())
                    <div class="footer-logo">
                        <img src="{{ $logo }}" alt="{{ tsetting('site_name') }}">
                    </div>
                    <h5 class="text-white mb-3">{{ tsetting('site_name') }}</h5>
                @else
                    <h5 class="text-white mb-3"><i class="fas fa-mortar-pestle text-ph"></i> {{ tsetting('site_name') }}</h5>
                @endif
                <p class="small">{{ tsetting('about') }}</p>
            </div>
            <div class="col-md-4">
                <h6 class="text-white mb-3">{{ __('site.quick_links') }}</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="{{ route('home') }}">{{ __('site.home') }}</a></li>
                    <li class="mb-2"><a href="{{ route('medicines.index') }}">{{ __('site.medicines') }}</a></li>
                    <li class="mb-2"><a href="{{ route('consultation.create') }}">{{ __('site.consultation') }}</a></li>
                    <li class="mb-2"><a href="{{ route('admin.index') }}">{{ __('site.admin_login') }}</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h6 class="text-white mb-3">{{ __('site.contact_us') }}</h6>
                <ul class="list-unstyled small">
                    @if (setting('phone'))
                        <li class="mb-2"><i class="fas fa-phone me-2"></i> {{ setting('phone') }}</li>
                    @endif
                    @if (setting('email'))
                        <li class="mb-2"><i class="fas fa-envelope me-2"></i> {{ setting('email') }}</li>
                    @endif
                    @if (tsetting('address'))
                        <li class="mb-2"><i class="fas fa-location-dot me-2"></i> {{ tsetting('address') }}</li>
                    @endif
                    @if (tsetting('working_hours'))
                        <li class="mb-2"><i class="fas fa-clock me-2"></i> {{ tsetting('working_hours') }}</li>
                    @endif
                </ul>
            </div>
        </div>
        <hr class="border-secondary my-4">
        <p class="text-center small mb-0">
            &copy; {{ date('Y') }} {{ tsetting('site_name') }} — {{ __('site.rights_reserved') }}
        </p>
    </div>
</footer>
