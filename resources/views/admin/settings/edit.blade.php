@extends('admin.master')

@section('content')
<style>
    .sticky-save-bar { position: sticky; top: 8px; z-index: 1020; background: #fff; padding: 10px 0; border-bottom: 1px solid #e9ecef; box-shadow: 0 4px 10px rgba(15,23,42,.08); }
    /* The two slider choices read as cards; the picked one is highlighted */
    .hero-option {
        border: 2px solid #e3e6f0;
        border-radius: .5rem;
        padding: 1rem;
        height: 100%;
        transition: border-color .2s, background .2s;
    }
    .hero-option.is-active { border-color: #0ea5a4; background: #f0fdfa; }
    .hero-option.is-off { opacity: .55; }
    .hero-swatch {
        width: 34px;
        height: 24px;
        border: 0;
        padding: 0;
        background: none;
        cursor: pointer;
    }
    #heroPreview {
        height: 130px;
        border-radius: .5rem;
        background: linear-gradient(135deg, #0f766e, #0ea5a4);
        background-size: cover;
        background-position: center;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: #fff;
        text-shadow: 0 1px 3px rgba(0, 0, 0, .35);
        padding: 1rem;
    }
    .hero-preview-text strong { display: block; font-size: 1.1rem; }
    .hero-preview-text span { font-size: .85rem; opacity: .9; }
    /* Chequerboard so a logo with a transparent background is still visible */
    .logo-box {
        width: 130px; height: 90px; flex: 0 0 auto;
        border: 2px dashed #e3e6f0; border-radius: .5rem;
        display: flex; align-items: center; justify-content: center;
        padding: .5rem; font-size: 26px;
        background-color: #fff;
        background-image:
            linear-gradient(45deg, #f1f3f9 25%, transparent 25%, transparent 75%, #f1f3f9 75%),
            linear-gradient(45deg, #f1f3f9 25%, transparent 25%, transparent 75%, #f1f3f9 75%);
        background-size: 16px 16px;
        background-position: 0 0, 8px 8px;
    }
    .logo-box img { max-width: 100%; max-height: 100%; object-fit: contain; }
    /* Saved slider pictures; ticking one marks it for deletion on save */
    .hero-thumbs { display: flex; flex-wrap: wrap; gap: .5rem; }
    .hero-thumb {
        position: relative; width: 92px; height: 62px; margin: 0;
        border: 2px solid #e3e6f0; border-radius: .4rem; overflow: hidden; cursor: pointer;
    }
    .hero-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .hero-thumb input { position: absolute; opacity: 0; pointer-events: none; }
    .hero-thumb-order {
        position: absolute; top: 2px; left: 2px; right: auto;
        background: rgba(0, 0, 0, .55); color: #fff;
        font-size: .7rem; line-height: 1; padding: 2px 5px; border-radius: .3rem;
    }
    .hero-thumb-mark {
        position: absolute; inset: 0; display: none;
        align-items: center; justify-content: center;
        background: rgba(220, 53, 69, .78); color: #fff; font-size: 18px;
    }
    .hero-thumb input:checked ~ .hero-thumb-mark { display: flex; }
    .hero-thumb input:checked ~ img { opacity: .5; }
    .hero-thumb:has(input:checked) { border-color: #dc3545; }
</style>

<h1 class="h3 mb-4 text-gray-800">{{ __('admin.site_settings') }}</h1>

@if (session()->has('msg'))
<div class="alert alert-{{ session('type') }} alert-dismissible fade show" role="alert">
    {{ session('msg') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
</div>
@endif

@php
    $val = fn($k) => old($k, $settings[$k] ?? '');
@endphp

<form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('put')
    <div class="sticky-save-bar mb-3">
        <button class="btn btn-primary"><i class="fas fa-save"></i> {{ __('admin.save') }}</button>
    </div>

    <div class="card mb-4">
        <div class="card-header"><strong>{{ __('admin.general') }}</strong></div>
        <div class="card-body row">
            <div class="col-md-6 mb-3">
                <label>{{ __('admin.site_name') }} (AR)</label>
                <input type="text" name="site_name_ar" class="form-control" value="{{ $val('site_name_ar') }}">
            </div>
            <div class="col-md-6 mb-3">
                <label>{{ __('admin.site_name') }} (EN)</label>
                <input type="text" name="site_name_en" class="form-control" value="{{ $val('site_name_en') }}">
            </div>

            {{-- Logo: shown in the site header, the dashboard sidebar and the browser tab --}}
            @php $site_logo = $settings['site_logo'] ?? ''; @endphp
            <div class="col-12">
                <hr>
                <label class="d-block"><i class="fas fa-image text-info"></i> {{ __('admin.site_logo') }}</label>
                <small class="text-muted d-block mb-2">{{ __('admin.site_logo_hint') }}</small>

                <div class="d-flex align-items-center flex-wrap" style="gap: 1rem;">
                    @php $logo = logo_url(); @endphp
                    <div class="logo-box">
                        {{-- The src is left off when there is no logo: an empty src makes
                             the browser re-request the page itself --}}
                        <img @if ($logo) src="{{ $logo }}" @endif alt="{{ __('admin.site_logo') }}"
                             id="logoPreview" class="{{ $logo ? '' : 'd-none' }}">
                        <span class="text-muted {{ $logo ? 'd-none' : '' }}" id="logoEmpty">
                            <i class="fas fa-mortar-pestle"></i>
                        </span>
                    </div>

                    <div class="flex-grow-1">
                        <input type="file" name="site_logo" id="logoInput" accept="image/*"
                               class="form-control-file @error('site_logo') is-invalid @enderror">
                        @error('site_logo')<small class="invalid-feedback d-block">{{ $message }}</small>@enderror

                        @if ($site_logo)
                            <div class="custom-control custom-checkbox mt-2">
                                <input type="checkbox" class="custom-control-input" id="removeSiteLogo"
                                       name="remove_site_logo" value="1">
                                <label class="custom-control-label small text-danger" for="removeSiteLogo">
                                    {{ __('admin.remove_logo') }}
                                </label>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header"><strong>{{ __('admin.contact_info') }}</strong></div>
        <div class="card-body row">
            @php
                $countryCodes = ['+966' => 'السعودية SA', '+971' => 'الإمارات AE', '+965' => 'الكويت KW',
                    '+974' => 'قطر QA', '+973' => 'البحرين BH', '+968' => 'عُمان OM', '+962' => 'الأردن JO',
                    '+970' => 'فلسطين PS', '+972' => 'فلسطين PS', '+20' => 'مصر EG', '+964' => 'العراق IQ', '+963' => 'سوريا SY',
                    '+961' => 'لبنان LB', '+218' => 'ليبيا LY', '+216' => 'تونس TN', '+213' => 'الجزائر DZ',
                    '+212' => 'المغرب MA', '+90' => 'تركيا TR', '+1' => 'US', '+44' => 'GB'];
                $currentCode = $val('default_country_code') ?: '+966';
            @endphp
            <div class="col-md-4 mb-3">
                <label>{{ __('admin.default_country') }}</label>
                <select name="default_country_code" class="form-control">
                    @foreach ($countryCodes as $code => $label)
                        <option value="{{ $code }}" {{ $currentCode == $code ? 'selected' : '' }}>{{ $label }} ({{ $code }})</option>
                    @endforeach
                </select>
                <small class="text-muted">{{ __('admin.default_country_hint') }}</small>
            </div>
            <div class="col-md-4 mb-3">
                <label>{{ __('admin.whatsapp_number') }}</label>
                <input type="text" name="whatsapp_number" class="form-control" placeholder="9665xxxxxxxx" value="{{ $val('whatsapp_number') }}">
                <small class="text-muted">{{ __('admin.whatsapp_hint') }}</small>
            </div>
            <div class="col-md-4 mb-3">
                <label>{{ __('admin.phone') }}</label>
                <input type="text" name="phone" class="form-control" value="{{ $val('phone') }}">
            </div>
            <div class="col-md-4 mb-3">
                <label>{{ __('admin.email') }}</label>
                <input type="email" name="email" class="form-control" value="{{ $val('email') }}">
            </div>
            <div class="col-md-6 mb-3">
                <label>{{ __('admin.address') }} (AR)</label>
                <input type="text" name="address_ar" class="form-control" value="{{ $val('address_ar') }}">
            </div>
            <div class="col-md-6 mb-3">
                <label>{{ __('admin.address') }} (EN)</label>
                <input type="text" name="address_en" class="form-control" value="{{ $val('address_en') }}">
            </div>
            <div class="col-md-6 mb-3">
                <label>{{ __('admin.working_hours') }} (AR)</label>
                <input type="text" name="working_hours_ar" class="form-control" value="{{ $val('working_hours_ar') }}">
            </div>
            <div class="col-md-6 mb-3">
                <label>{{ __('admin.working_hours') }} (EN)</label>
                <input type="text" name="working_hours_en" class="form-control" value="{{ $val('working_hours_en') }}">
            </div>
        </div>
    </div>

    {{-- ===== Homepage slider ===== --}}
    @php
        $hero_mode    = $val('hero_mode') ?: 'colors';
        $hero_from    = $val('hero_color_from') ?: '#0f766e';
        $hero_to      = $val('hero_color_to') ?: '#0ea5a4';
        $hero_overlay = $val('hero_overlay') !== '' ? $val('hero_overlay') : 40;
        $hero_slides  = hero_images();
    @endphp
    <div class="card mb-4">
        <div class="card-header">
            <strong><i class="fas fa-images"></i> {{ __('admin.slider') }}</strong>
            <small class="text-muted d-block">{{ __('admin.slider_hint') }}</small>
        </div>
        <div class="card-body">
            <div class="row">
                {{-- Option 1: the colour gradient it uses today --}}
                <div class="col-md-6 mb-3">
                    <div class="hero-option {{ $hero_mode === 'colors' ? 'is-active' : '' }}" id="heroOptionColors">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="custom-control custom-radio">
                                <input type="radio" class="custom-control-input hero-mode" id="heroModeColors"
                                       name="hero_mode" value="colors" {{ $hero_mode === 'colors' ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-bold" for="heroModeColors">
                                    <i class="fas fa-palette text-info"></i> {{ __('admin.slider_colors') }}
                                </label>
                            </div>
                            {{-- Back to the gradient the site shipped with --}}
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="heroResetColors"
                                    data-from="#0f766e" data-to="#0ea5a4"
                                    title="{{ __('admin.reset_default_hint') }}">
                                <i class="fas fa-rotate-left"></i> {{ __('admin.reset_default') }}
                            </button>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-6">
                                <label class="small">{{ __('admin.color_from') }}</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text p-1">
                                            <input type="color" class="hero-swatch" data-hex="heroFromHex"
                                                   value="{{ $hero_from }}" aria-label="{{ __('admin.color_from') }}">
                                        </span>
                                    </div>
                                    <input type="text" name="hero_color_from" id="heroFromHex" dir="ltr"
                                           class="form-control @error('hero_color_from') is-invalid @enderror"
                                           value="{{ $hero_from }}" placeholder="#0f766e">
                                    @error('hero_color_from')<small class="invalid-feedback">{{ $message }}</small>@enderror
                                </div>
                            </div>
                            <div class="form-group col-6">
                                <label class="small">{{ __('admin.color_to') }}</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text p-1">
                                            <input type="color" class="hero-swatch" data-hex="heroToHex"
                                                   value="{{ $hero_to }}" aria-label="{{ __('admin.color_to') }}">
                                        </span>
                                    </div>
                                    <input type="text" name="hero_color_to" id="heroToHex" dir="ltr"
                                           class="form-control @error('hero_color_to') is-invalid @enderror"
                                           value="{{ $hero_to }}" placeholder="#0ea5a4">
                                    @error('hero_color_to')<small class="invalid-feedback">{{ $message }}</small>@enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Option 2: a picture from the manager's files --}}
                <div class="col-md-6 mb-3">
                    <div class="hero-option {{ $hero_mode === 'image' ? 'is-active' : '' }}" id="heroOptionImage">
                        <div class="custom-control custom-radio mb-3">
                            <input type="radio" class="custom-control-input hero-mode" id="heroModeImage"
                                   name="hero_mode" value="image" {{ $hero_mode === 'image' ? 'checked' : '' }}>
                            <label class="custom-control-label font-weight-bold" for="heroModeImage">
                                <i class="fas fa-image text-success"></i> {{ __('admin.slider_image') }}
                            </label>
                        </div>

                        <div class="form-group">
                            <input type="file" name="hero_images[]" id="heroImageInput" accept="image/*" multiple
                                   class="form-control-file @error('hero_images.*') is-invalid @enderror">
                            @error('hero_images.*')<small class="invalid-feedback d-block">{{ $message }}</small>@enderror
                            @error('hero_images')<small class="invalid-feedback d-block">{{ $message }}</small>@enderror
                            <small class="text-muted d-block">{{ __('admin.slider_image_hint') }}</small>
                            <small class="text-muted">{{ __('admin.slider_images_hint') }}</small>
                        </div>

                        {{-- What the slider holds today; tick one to drop it on save --}}
                        @if ($hero_slides)
                            <label class="small text-muted d-block">
                                {{ __('admin.slider_current_images') }} ({{ count($hero_slides) }})
                            </label>
                            <div class="hero-thumbs mb-2">
                                @foreach ($hero_slides as $index => $slide)
                                    <label class="hero-thumb" title="{{ __('admin.remove_slider_image') }}">
                                        {{-- The box comes first so the CSS can dim the picture behind it --}}
                                        <input type="checkbox" name="remove_hero_images[]" value="{{ $slide }}">
                                        <img src="{{ asset('images/' . $slide) }}" alt="">
                                        <span class="hero-thumb-mark"><i class="fas fa-trash"></i></span>
                                        <span class="hero-thumb-order">{{ $index + 1 }}</span>
                                    </label>
                                @endforeach
                            </div>
                        @endif

                        <div class="form-group mb-0">
                            <label class="small">{{ __('admin.slider_overlay') }}</label>
                            <input type="range" name="hero_overlay" id="heroOverlay" class="custom-range"
                                   min="0" max="90" step="5" value="{{ $hero_overlay }}">
                            <small class="text-muted"><span id="heroOverlayValue">{{ $hero_overlay }}</span>%
                                — {{ __('admin.slider_overlay_hint') }}</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Live preview of whichever option is picked --}}
            <label class="small text-muted d-block">{{ __('admin.preview') }}</label>
            <div id="heroPreview">
                <div class="hero-preview-text">
                    <strong>{{ $val('hero_title_ar') ?: tsetting('site_name') }}</strong>
                    <span>{{ $val('hero_subtitle_ar') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header"><strong>{{ __('admin.homepage_content') }}</strong></div>
        <div class="card-body row">
            <div class="col-md-6 mb-3">
                <label>{{ __('admin.hero_title') }} (AR)</label>
                <input type="text" name="hero_title_ar" class="form-control" value="{{ $val('hero_title_ar') }}">
            </div>
            <div class="col-md-6 mb-3">
                <label>{{ __('admin.hero_title') }} (EN)</label>
                <input type="text" name="hero_title_en" class="form-control" value="{{ $val('hero_title_en') }}">
            </div>
            <div class="col-md-6 mb-3">
                <label>{{ __('admin.hero_subtitle') }} (AR)</label>
                <textarea name="hero_subtitle_ar" rows="2" class="form-control">{{ $val('hero_subtitle_ar') }}</textarea>
            </div>
            <div class="col-md-6 mb-3">
                <label>{{ __('admin.hero_subtitle') }} (EN)</label>
                <textarea name="hero_subtitle_en" rows="2" class="form-control">{{ $val('hero_subtitle_en') }}</textarea>
            </div>
            <div class="col-md-6 mb-3">
                <label>{{ __('admin.about') }} (AR)</label>
                <textarea name="about_ar" rows="3" class="form-control">{{ $val('about_ar') }}</textarea>
            </div>
            <div class="col-md-6 mb-3">
                <label>{{ __('admin.about') }} (EN)</label>
                <textarea name="about_en" rows="3" class="form-control">{{ $val('about_en') }}</textarea>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header"><strong>{{ __('admin.social_media') }}</strong>
            <small class="text-muted d-block">{{ __('admin.social_hint') }}</small>
        </div>
        <div class="card-body row">
            <div class="col-md-6 mb-3">
                <label><i class="fab fa-facebook-f"></i> Facebook</label>
                <input type="url" name="social_facebook" class="form-control" placeholder="https://facebook.com/..." value="{{ $val('social_facebook') }}">
            </div>
            <div class="col-md-6 mb-3">
                <label><i class="fab fa-instagram"></i> Instagram</label>
                <input type="url" name="social_instagram" class="form-control" placeholder="https://instagram.com/..." value="{{ $val('social_instagram') }}">
            </div>
            <div class="col-md-6 mb-3">
                <label><i class="fab fa-youtube"></i> YouTube</label>
                <input type="url" name="social_youtube" class="form-control" placeholder="https://youtube.com/..." value="{{ $val('social_youtube') }}">
            </div>
            <div class="col-md-6 mb-3">
                <label><i class="fab fa-telegram"></i> Telegram</label>
                <input type="url" name="social_telegram" class="form-control" placeholder="https://t.me/..." value="{{ $val('social_telegram') }}">
            </div>
            <div class="col-md-6 mb-3">
                <label><i class="fab fa-x-twitter"></i> X (Twitter)</label>
                <input type="url" name="social_twitter" class="form-control" placeholder="https://x.com/..." value="{{ $val('social_twitter') }}">
            </div>
            <div class="col-md-6 mb-3">
                <label><i class="fab fa-tiktok"></i> TikTok</label>
                <input type="url" name="social_tiktok" class="form-control" placeholder="https://tiktok.com/@..." value="{{ $val('social_tiktok') }}">
            </div>
            <div class="col-md-6 mb-3">
                <label><i class="fab fa-snapchat"></i> Snapchat</label>
                <input type="url" name="social_snapchat" class="form-control" placeholder="https://snapchat.com/add/..." value="{{ $val('social_snapchat') }}">
            </div>
            <div class="col-md-6 mb-3">
                <label><i class="fab fa-linkedin-in"></i> LinkedIn</label>
                <input type="url" name="social_linkedin" class="form-control" placeholder="https://linkedin.com/..." value="{{ $val('social_linkedin') }}">
            </div>
        </div>
    </div>

    <button class="btn btn-primary btn-lg">{{ __('admin.save') }}</button>
</form>

{{-- The layout loads jQuery after the page content, so wire up on DOMContentLoaded --}}
<script>
// Logo: show the picked file straight away instead of waiting for the save
document.addEventListener('DOMContentLoaded', function () {
    const input  = document.getElementById('logoInput');
    const img    = document.getElementById('logoPreview');
    const empty  = document.getElementById('logoEmpty');
    const remove = document.getElementById('removeSiteLogo');
    const saved  = img ? img.getAttribute('src') : '';

    if (!input) return;

    function show(url) {
        if (url) {
            img.setAttribute('src', url);
        } else {
            img.removeAttribute('src');
        }

        img.classList.toggle('d-none', !url);
        empty.classList.toggle('d-none', !!url);
    }

    input.addEventListener('change', function () {
        const [file] = input.files;
        if (!file) return;

        show(URL.createObjectURL(file));
        if (remove) remove.checked = false;
    });

    if (remove) {
        remove.addEventListener('change', function () {
            if (remove.checked) {
                input.value = '';
                show('');
            } else {
                show(saved);
            }
        });
    }
});

document.addEventListener('DOMContentLoaded', function () {
    const preview = document.getElementById('heroPreview');
    const modes   = document.querySelectorAll('.hero-mode');
    const from    = document.getElementById('heroFromHex');
    const to      = document.getElementById('heroToHex');
    const overlay = document.getElementById('heroOverlay');
    const overlayValue = document.getElementById('heroOverlayValue');
    const fileInput = document.getElementById('heroImageInput');

    if (!preview) return;

    // The saved pictures, replaced live when new files are picked
    let images = @json($hero_slides ? array_map(fn ($s) => asset('images/' . $s), $hero_slides) : []);
    let shown = 0;

    function pickedMode() {
        const picked = document.querySelector('.hero-mode:checked');
        return picked ? picked.value : 'colors';
    }

    function isHex(value) {
        return /^#[0-9a-fA-F]{6}$/.test(value);
    }

    /** Paint the preview the same way the site will render the slider. */
    function render() {
        const mode = pickedMode();

        document.getElementById('heroOptionColors').classList.toggle('is-active', mode === 'colors');
        document.getElementById('heroOptionColors').classList.toggle('is-off', mode !== 'colors');
        document.getElementById('heroOptionImage').classList.toggle('is-active', mode === 'image');
        document.getElementById('heroOptionImage').classList.toggle('is-off', mode !== 'image');

        if (mode === 'image' && images.length) {
            const shade = (parseInt(overlay.value, 10) || 0) / 100;
            preview.style.background =
                'linear-gradient(rgba(0,0,0,' + shade + '), rgba(0,0,0,' + shade + ')), url("' + images[shown % images.length] + '")';
            preview.style.backgroundSize = 'cover';
            preview.style.backgroundPosition = 'center';
        } else {
            const a = isHex(from.value) ? from.value : '#0f766e';
            const b = isHex(to.value) ? to.value : '#0ea5a4';
            preview.style.background = 'linear-gradient(135deg, ' + a + ', ' + b + ')';
        }
    }

    // Native colour picker keeps its hex field in step, and the other way round
    document.querySelectorAll('.hero-swatch').forEach(function (swatch) {
        const hex = document.getElementById(swatch.dataset.hex);

        swatch.addEventListener('input', function () {
            hex.value = swatch.value;
            render();
        });
        hex.addEventListener('input', function () {
            if (isHex(hex.value)) swatch.value = hex.value;
            render();
        });
    });

    modes.forEach(function (mode) { mode.addEventListener('change', render); });

    // Put the original gradient back in both the hex fields and their swatches
    const resetColors = document.getElementById('heroResetColors');
    if (resetColors) {
        resetColors.addEventListener('click', function () {
            from.value = resetColors.dataset.from;
            to.value = resetColors.dataset.to;

            document.querySelectorAll('.hero-swatch').forEach(function (swatch) {
                swatch.value = document.getElementById(swatch.dataset.hex).value;
            });

            document.getElementById('heroModeColors').checked = true;
            render();
        });
    }

    if (overlay) {
        overlay.addEventListener('input', function () {
            overlayValue.textContent = overlay.value;
            render();
        });
    }

    if (fileInput) {
        fileInput.addEventListener('change', function () {
            if (!fileInput.files.length) return;

            // The picked files go on the end, just as the save will place them
            images = images.concat(Array.from(fileInput.files).map(URL.createObjectURL));

            // Picking a file is a clear signal the manager wants the picture slider
            document.getElementById('heroModeImage').checked = true;
            render();
        });
    }

    // Ticking a saved picture for deletion takes it out of the preview too
    document.querySelectorAll('.hero-thumb input').forEach(function (box) {
        box.addEventListener('change', function () {
            const url = box.closest('.hero-thumb').querySelector('img').src;

            images = box.checked
                ? images.filter(function (image) { return image !== url; })
                : images.concat([url]);

            render();
        });
    });

    // Show the pictures in turn, the way the homepage fades through them
    if (images.length > 1) {
        setInterval(function () {
            shown++;
            render();
        }, 2500);
    }

    render();
});
</script>
@endsection

@section('title', 'Settings')
