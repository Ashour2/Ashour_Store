<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

class SettingController extends Controller
{
    /**
     * Keys the admin is allowed to manage from the settings screen.
     */
    protected array $keys = [
        'site_name_ar', 'site_name_en',
        'whatsapp_number', 'phone', 'email', 'default_country_code',
        'address_ar', 'address_en',
        'hero_title_ar', 'hero_title_en',
        'hero_subtitle_ar', 'hero_subtitle_en',
        'about_ar', 'about_en',
        'working_hours_ar', 'working_hours_en',
        'social_facebook', 'social_instagram', 'social_youtube', 'social_telegram',
        'social_twitter', 'social_tiktok', 'social_snapchat', 'social_linkedin',
    ];

    /** How many slider pictures the homepage will rotate through. */
    const HERO_MAX_IMAGES = 10;

    /** Fallback gradient, matching the site's own palette. */
    const HERO_DEFAULTS = [
        'hero_color_from' => '#0f766e',
        'hero_color_to'   => '#0ea5a4',
        'hero_overlay'    => '40',
    ];

    public function edit()
    {
        $settings = Setting::pluck('value', 'key')->toArray();

        return view('admin.settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'hero_mode'       => 'nullable|in:colors,image',
            'hero_color_from' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'hero_color_to'   => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'hero_overlay'    => 'nullable|integer|min:0|max:90',
            'hero_images'     => 'nullable|array|max:' . self::HERO_MAX_IMAGES,
            'hero_images.*'   => 'image|mimes:jpg,jpeg,png,webp,gif|max:4096',
            'site_logo'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        foreach ($this->keys as $key) {
            Setting::put($key, $request->input($key));
        }

        $this->saveHero($request);

        // Shown in the site header, the dashboard sidebar and as the favicon
        $this->saveUpload($request, 'site_logo', 'logo');

        return redirect()
            ->route('admin.settings.edit')
            ->with('msg', __('admin.settings_saved'))
            ->with('type', 'success');
    }

    /**
     * The homepage slider is either the colour gradient or a set of pictures
     * the page fades through.
     */
    protected function saveHero(Request $request): void
    {
        Setting::put('hero_mode', $request->input('hero_mode') === 'image' ? 'image' : 'colors');

        foreach (self::HERO_DEFAULTS as $key => $default) {
            Setting::put($key, $request->filled($key) ? $request->input($key) : $default);
        }

        $this->saveHeroImages($request);
    }

    /**
     * Add the newly picked pictures to the slider and drop the ones ticked for
     * removal, keeping the order they are shown in.
     */
    protected function saveHeroImages(Request $request): void
    {
        $images = hero_images();

        foreach ((array) $request->input('remove_hero_images', []) as $name) {
            $position = array_search($name, $images, true);

            if ($position !== false) {
                File::delete(public_path('images/' . $name));
                unset($images[$position]);
            }
        }

        foreach ((array) $request->file('hero_images', []) as $file) {
            $images[] = $this->moveUpload($file, 'hero');
        }

        $images = array_slice(array_values($images), 0, self::HERO_MAX_IMAGES);

        Setting::put('hero_images', json_encode($images));

        // A site saved before the slider took several pictures kept a single
        // name here; hero_images() has just folded it in, so let it go.
        if (Setting::get('hero_image')) {
            Setting::put('hero_image', null);
        }
    }

    /**
     * Keep one uploaded image in public/images under the setting $key: a new file
     * replaces whatever is there, and the matching remove_<key> checkbox clears it.
     */
    protected function saveUpload(Request $request, string $key, string $prefix): void
    {
        if ($request->boolean('remove_' . $key)) {
            $this->deleteUpload($key);
            Setting::put($key, null);
        }

        if ($request->hasFile($key)) {
            $this->deleteUpload($key);

            Setting::put($key, $this->moveUpload($request->file($key), $prefix));
        }
    }

    /**
     * Move an upload into public/images and return the name it was stored under.
     *
     * The name is generated rather than taken from the upload: it ends up inside
     * a url(...) in the site's stylesheet, where a quote or a bracket in the
     * original filename could break out of the rule.
     *
     * The extension comes from the image's real type, not the client's filename:
     * Apache has no mapping for oddities like .jfif and would serve them as
     * octet-stream, leaving the image blank.
     */
    protected function moveUpload(UploadedFile $file, string $prefix): string
    {
        $extension = strtolower($file->extension() ?: $file->getClientOriginalExtension() ?: 'jpg');
        $name = $prefix . '-' . time() . '-' . mt_rand(1000, 9999) . '.' . $extension;

        $file->move(public_path('images'), $name);

        return $name;
    }

    /** Drop the previous file behind $key so replaced images do not pile up. */
    protected function deleteUpload(string $key): void
    {
        $current = Setting::get($key);

        if ($current) {
            File::delete(public_path('images/' . $current));
        }
    }
}
