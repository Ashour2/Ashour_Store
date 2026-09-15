{{--
    The slider pictures, stacked behind the hero wording and cross-faded in
    plain CSS: one shared animation, each layer offset by its own delay, so any
    number of pictures rotates without a script.
--}}
@php
    $slides = hero_images();
    $shade  = min(90, max(0, (int) setting('hero_overlay', 40))) / 100;

    $seconds = 5;                          // how long one picture holds
    $cycle   = count($slides) * $seconds;  // a full turn through the set
    $share   = 100 / max(1, count($slides));
    $fade    = round(min(6, $share / 3), 2);
@endphp

@if ($slides)
<style>
    .hero-slide {
        position: absolute;
        inset: 0;
        background-size: cover;
        background-position: center;
        z-index: 1;
    }

    @if (count($slides) > 1)
        .hero-slide { opacity: 0; animation: heroSlide {{ $cycle }}s infinite; }

        @keyframes heroSlide {
            0%                            { opacity: 0; }
            {{ $fade }}%                  { opacity: 1; }
            {{ round($share, 2) }}%       { opacity: 1; }
            {{ round($share + $fade, 2) }}% { opacity: 0; }
            100%                          { opacity: 0; }
        }

        /* Anyone who asked for less movement just gets the first picture */
        @media (prefers-reduced-motion: reduce) {
            .hero-slide { animation: none; opacity: 0; }
            .hero-slide:first-child { opacity: 1; }
        }
    @endif
</style>

@foreach ($slides as $index => $slide)
    <div class="hero-slide" aria-hidden="true"
         style="background-image: linear-gradient(rgba(0,0,0,{{ $shade }}), rgba(0,0,0,{{ $shade }})), url('{{ asset('images/' . $slide) }}');
                @if (count($slides) > 1) animation-delay: {{ $index * $seconds }}s; @endif"></div>
@endforeach
@endif
