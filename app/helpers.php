<?php

use App\Models\Setting;

if (! function_exists('setting')) {
    /**
     * Read a dynamic site setting managed from the admin dashboard.
     */
    function setting(string $key, $default = null)
    {
        return Setting::get($key, $default);
    }
}

if (! function_exists('tsetting')) {
    /**
     * Read a bilingual setting for the current locale, e.g. tsetting('site_name')
     * reads site_name_ar / site_name_en depending on the active language.
     */
    function tsetting(string $base, $default = null)
    {
        $locale = app()->getLocale();
        return Setting::get($base . '_' . $locale, Setting::get($base . '_en', $default));
    }
}

if (! function_exists('cart')) {
    /**
     * The session shopping cart, e.g. cart()->count() / cart()->items().
     */
    function cart(): \App\Services\CartService
    {
        return app(\App\Services\CartService::class);
    }
}

if (! function_exists('logo_url')) {
    /**
     * Public URL of the logo uploaded from the dashboard, or null when none is set.
     *
     * The stored name is re-checked against the disk because the logo is printed
     * into the <head> of every page, the dashboard included: a setting left over
     * from a deleted file would otherwise show as a broken image everywhere.
     */
    function logo_url(): ?string
    {
        $file = (string) Setting::get('site_logo');

        if (! preg_match('/^[A-Za-z0-9._-]+$/', $file) || ! file_exists(public_path('images/' . $file))) {
            return null;
        }

        return asset('images/' . $file);
    }
}

if (! function_exists('hero_images')) {
    /**
     * The slider pictures chosen in the dashboard, newest last, as filenames.
     *
     * Each name is re-checked against the disk because it is written straight
     * into a url(...) in the page stylesheet, where HTML escaping protects
     * nothing and a record left over from a deleted file shows as a blank slide.
     *
     * A site saved before the slider took several pictures still holds a single
     * name under hero_image; it is read here until the next save folds it in.
     */
    function hero_images(): array
    {
        $names = json_decode((string) Setting::get('hero_images'), true);

        if (! is_array($names) || empty($names)) {
            $names = array_filter([(string) Setting::get('hero_image')]);
        }

        return array_values(array_filter($names, function ($name) {
            return is_string($name)
                && preg_match('/^[A-Za-z0-9._-]+$/', $name)
                && file_exists(public_path('images/' . $name));
        }));
    }
}
