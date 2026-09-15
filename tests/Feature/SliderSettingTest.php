<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolesPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Tests\TestCase;

class SliderSettingTest extends TestCase
{
    use RefreshDatabase;

    /** Slider images land in public/images; remember what we add so we can clean up. */
    protected array $uploaded = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([LaravelLocalizationRedirectFilter::class, LocaleSessionRedirect::class]);
        $this->seed(RolesPermissionsSeeder::class);
    }

    protected function tearDown(): void
    {
        foreach ($this->uploaded as $name) {
            File::delete(public_path('images/' . $name));
        }

        parent::tearDown();
    }

    /** Several tests save the settings twice, so reuse the same admin. */
    protected function admin(): User
    {
        return User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Admin', 'password' => bcrypt('password'), 'type' => 'admin',
                'role_id' => Role::where('slug', Role::SUPER_ADMIN)->value('id'),
            ]
        );
    }

    /** The settings form posts every key at once, so send a full payload. */
    protected function payload(array $overrides = []): array
    {
        return $overrides + [
            'site_name_ar' => 'صيدلية', 'site_name_en' => 'Pharmacy',
            'hero_title_ar' => 'عنوان', 'hero_title_en' => 'Title',
        ];
    }

    protected function saveSettings(array $overrides = [])
    {
        return $this->actingAs($this->admin())
            ->put(route('admin.settings.update'), $this->payload($overrides));
    }

    /** The saved slider, as the site reads it. */
    protected function slides(): array
    {
        return hero_images();
    }

    protected function rememberUploads(): void
    {
        $this->uploaded = array_merge($this->uploaded, $this->slides());
    }

    public function test_the_settings_screen_offers_both_slider_options(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.settings.edit'))
            ->assertOk()
            ->assertSee('value="colors"', false)
            ->assertSee('value="image"', false)
            ->assertSee('type="color"', false)            // the colour control
            ->assertSee('name="hero_images[]"', false)    // the file picker
            ->assertSee('multiple', false)                // more than one at a time
            ->assertSee('enctype="multipart/form-data"', false);
    }

    public function test_colour_mode_saves_the_gradient(): void
    {
        $this->saveSettings([
            'hero_mode' => 'colors',
            'hero_color_from' => '#112233',
            'hero_color_to' => '#445566',
        ])->assertRedirect(route('admin.settings.edit'));

        $this->assertSame('colors', Setting::get('hero_mode'));
        $this->assertSame('#112233', Setting::get('hero_color_from'));
        $this->assertSame('#445566', Setting::get('hero_color_to'));
    }

    public function test_the_chosen_gradient_reaches_the_public_homepage(): void
    {
        $this->saveSettings([
            'hero_mode' => 'colors',
            'hero_color_from' => '#112233',
            'hero_color_to' => '#445566',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('linear-gradient(135deg, #112233, #445566)', false);
    }

    public function test_a_bad_colour_is_rejected(): void
    {
        Setting::put('hero_color_from', '#0f766e');

        $this->saveSettings(['hero_mode' => 'colors', 'hero_color_from' => 'red; background: url(evil)'])
            ->assertSessionHasErrors('hero_color_from');

        $this->assertSame('#0f766e', Setting::get('hero_color_from'));
    }

    public function test_image_mode_stores_the_uploaded_slider(): void
    {
        $this->saveSettings([
            'hero_mode' => 'image',
            'hero_images' => [UploadedFile::fake()->image('slider.jpg', 1600, 500)],
        ])->assertRedirect(route('admin.settings.edit'));

        $this->rememberUploads();

        $this->assertSame('image', Setting::get('hero_mode'));
        $this->assertCount(1, $slides = $this->slides());
        $this->assertStringEndsWith('.jpg', $slides[0]);
        $this->assertFileExists(public_path('images/' . $slides[0]));
    }

    public function test_several_pictures_are_kept_in_the_order_they_were_added(): void
    {
        $this->saveSettings([
            'hero_mode' => 'image',
            'hero_images' => [
                UploadedFile::fake()->image('one.jpg'),
                UploadedFile::fake()->image('two.jpg'),
            ],
        ]);
        $this->rememberUploads();
        $first = $this->slides();
        $this->assertCount(2, $first);

        // A later save adds to the slider rather than replacing it
        $this->saveSettings([
            'hero_mode' => 'image',
            'hero_images' => [UploadedFile::fake()->image('three.jpg')],
        ]);
        $this->rememberUploads();

        $slides = $this->slides();
        $this->assertCount(3, $slides);
        $this->assertSame($first, array_slice($slides, 0, 2));

        foreach ($slides as $slide) {
            $this->assertFileExists(public_path('images/' . $slide));
        }
    }

    public function test_the_homepage_fades_through_every_picture(): void
    {
        $this->saveSettings([
            'hero_mode' => 'image',
            'hero_images' => [
                UploadedFile::fake()->image('one.jpg'),
                UploadedFile::fake()->image('two.jpg'),
                UploadedFile::fake()->image('three.jpg'),
            ],
        ]);
        $this->rememberUploads();

        $response = $this->get(route('home'))->assertOk();

        foreach ($this->slides() as $index => $slide) {
            $response->assertSee("url('" . asset('images/' . $slide) . "')", false)
                ->assertSee('animation-delay: ' . ($index * 5) . 's', false);
        }

        // One shared animation drives all three layers
        $response->assertSee('@keyframes heroSlide', false)
            ->assertSee('animation: heroSlide 15s infinite', false);
    }

    public function test_a_single_picture_is_shown_without_animating(): void
    {
        $this->saveSettings([
            'hero_mode' => 'image',
            'hero_images' => [UploadedFile::fake()->image('only.jpg')],
        ]);
        $this->rememberUploads();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('hero-slide', false)
            ->assertDontSee('@keyframes heroSlide', false);
    }

    public function test_the_slider_holds_at_most_ten_pictures(): void
    {
        $files = [];

        foreach (range(1, 11) as $i) {
            $files[] = UploadedFile::fake()->image('slide-' . $i . '.jpg');
        }

        $this->saveSettings(['hero_mode' => 'image', 'hero_images' => $files])
            ->assertSessionHasErrors('hero_images');

        $this->assertEmpty($this->slides());
    }

    public function test_the_uploaded_slider_reaches_the_public_homepage(): void
    {
        $this->saveSettings([
            'hero_mode' => 'image',
            'hero_overlay' => 50,
            'hero_images' => [UploadedFile::fake()->image('slider.jpg', 1600, 500)],
        ]);

        $this->rememberUploads();
        $name = $this->slides()[0];

        $this->get(route('home'))
            ->assertOk()
            ->assertSee("url('" . asset('images/' . $name) . "')", false)
            ->assertSee('rgba(0,0,0,0.5)', false);
    }

    public function test_colour_mode_paints_no_slides_at_all(): void
    {
        // The pictures sit on top of the gradient, so the colours stay in the
        // stylesheet either way; what must not appear is a slide layer.
        $this->saveSettings([
            'hero_mode' => 'colors',
            'hero_color_from' => '#112233',
            'hero_color_to' => '#445566',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('linear-gradient(135deg, #112233, #445566)', false)
            ->assertDontSee('hero-slide', false);
    }

    public function test_a_non_image_upload_is_rejected(): void
    {
        $this->saveSettings([
            'hero_mode' => 'image',
            'hero_images' => [UploadedFile::fake()->create('notes.pdf', 100, 'application/pdf')],
        ])->assertSessionHasErrors('hero_images.0');

        $this->assertEmpty($this->slides());
    }

    public function test_one_picture_can_be_dropped_and_the_rest_stay(): void
    {
        $this->saveSettings([
            'hero_mode' => 'image',
            'hero_images' => [
                UploadedFile::fake()->image('first.jpg'),
                UploadedFile::fake()->image('second.jpg'),
            ],
        ]);
        $this->rememberUploads();

        [$first, $second] = $this->slides();

        $this->saveSettings(['hero_mode' => 'image', 'remove_hero_images' => [$first]]);

        $this->assertSame([$second], $this->slides());
        $this->assertFileDoesNotExist(public_path('images/' . $first));
        $this->assertFileExists(public_path('images/' . $second));
    }

    public function test_the_whole_slider_can_be_emptied(): void
    {
        $this->saveSettings([
            'hero_mode' => 'image',
            'hero_images' => [
                UploadedFile::fake()->image('first.jpg'),
                UploadedFile::fake()->image('second.jpg'),
            ],
        ]);
        $slides = $this->slides();

        $this->saveSettings(['hero_mode' => 'colors', 'remove_hero_images' => $slides]);

        $this->assertEmpty($this->slides());

        foreach ($slides as $slide) {
            $this->assertFileDoesNotExist(public_path('images/' . $slide));
        }
    }

    public function test_a_slider_saved_as_a_single_picture_is_carried_over(): void
    {
        // Sites saved before the slider took several pictures kept one name
        // under hero_image; it must survive as the first slide.
        $this->saveSettings([
            'hero_mode' => 'image',
            'hero_images' => [UploadedFile::fake()->image('legacy.jpg')],
        ]);
        $this->rememberUploads();

        $legacy = $this->slides()[0];
        Setting::put('hero_images', null);
        Setting::put('hero_image', $legacy);

        $this->assertSame([$legacy], $this->slides());
        $this->get(route('home'))->assertOk()->assertSee($legacy, false);

        // The next save folds it into the list and lets the old key go
        $this->saveSettings(['hero_mode' => 'image']);

        $this->assertSame([$legacy], $this->slides());
        $this->assertEmpty(Setting::get('hero_image'));
    }

    public function test_image_mode_falls_back_to_the_gradient_when_the_file_is_missing(): void
    {
        // A record pointing at a file that is no longer on disk must not break the page
        Setting::put('hero_mode', 'image');
        Setting::put('hero_image', 'does-not-exist.jpg');

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('linear-gradient(135deg', false)
            ->assertDontSee('does-not-exist.jpg', false);
    }

    public function test_the_stored_filename_is_generated_not_taken_from_the_upload(): void
    {
        // The name ends up inside a url(...) in the site stylesheet, so it is
        // generated rather than carried over from whatever was uploaded.
        $this->saveSettings([
            'hero_mode' => 'image',
            'hero_images' => [UploadedFile::fake()->image('my odd slider name.jpg')],
        ]);

        $this->rememberUploads();
        $name = $this->slides()[0];

        $this->assertMatchesRegularExpression('/^hero-\d+-\d+\.jpg$/', $name);
        $this->assertStringNotContainsString('odd', $name);
    }

    public function test_a_tampered_filename_in_the_database_falls_back_to_the_gradient(): void
    {
        Setting::put('hero_mode', 'image');
        Setting::put('hero_image', 'x.jpg"); background: url(http://evil.test/x.jpg');

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('linear-gradient(135deg', false)
            ->assertDontSee('evil.test', false);
    }

    public function test_only_an_admin_can_change_the_slider(): void
    {
        $customer = User::create([
            'name' => 'Sara', 'email' => 'sara@test.com',
            'password' => bcrypt('password'), 'type' => 'customer',
        ]);

        $this->actingAs($customer)
            ->put(route('admin.settings.update'), $this->payload(['hero_mode' => 'image']))
            ->assertRedirect('/');

        $this->put(route('admin.settings.update'), $this->payload())->assertRedirect();
    }
}
