@php
    $socials = [
        'social_facebook'  => ['icon' => 'fa-brands fa-facebook-f', 'color' => '#1877f2', 'label' => 'Facebook'],
        'social_instagram' => ['icon' => 'fa-brands fa-instagram',  'color' => '#e4405f', 'label' => 'Instagram'],
        'social_youtube'   => ['icon' => 'fa-brands fa-youtube',    'color' => '#ff0000', 'label' => 'YouTube'],
        'social_telegram'  => ['icon' => 'fa-brands fa-telegram',   'color' => '#229ED9', 'label' => 'Telegram'],
        'social_twitter'   => ['icon' => 'fa-brands fa-x-twitter',  'color' => '#000000', 'label' => 'X'],
        'social_tiktok'    => ['icon' => 'fa-brands fa-tiktok',     'color' => '#010101', 'label' => 'TikTok'],
        'social_snapchat'  => ['icon' => 'fa-brands fa-snapchat',   'color' => '#fffc00', 'label' => 'Snapchat'],
        'social_linkedin'  => ['icon' => 'fa-brands fa-linkedin-in','color' => '#0a66c2', 'label' => 'LinkedIn'],
    ];
    $active = collect($socials)->filter(fn ($s, $k) => filled(setting($k)));
@endphp

@if ($active->count())
<div class="social-rail">
    @foreach ($active as $key => $s)
        <a href="{{ setting($key) }}" target="_blank" rel="noopener"
           class="social-rail-btn" title="{{ $s['label'] }}"
           style="--sc: {{ $s['color'] }}; {{ $s['label'] === 'Snapchat' ? 'color:#000;' : '' }}">
            <i class="{{ $s['icon'] }}"></i>
        </a>
    @endforeach
</div>
@endif
