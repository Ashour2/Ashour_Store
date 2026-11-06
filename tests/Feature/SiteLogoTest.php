<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\RolesPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Tests\TestCase;

class SiteLogoTest extends TestCase
{
    use RefreshDatabase;

    /** Logos land in public/images; remember what we add so we can clean up. */
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
    protected function saveSettings(array $overrides = [])
    {
        return $this->actingAs($this->admin())
            ->put(route('admin.settings.update'), $overrides + [
                'site_name_ar' => 'صيدلية', 'site_name_en' => 'Pharmacy',
            ]);
    }

    protected function uploadLogo(string $filename = 'logo.png'): string
    {
        $this->saveSettings(['site_logo' => UploadedFile::fake()->image($filename, 300, 120)]);

        $name = Setting::get('site_logo');
        $this->uploaded[] = $name;

        return $name;
    }

    public function test_the_settings_screen_offers_a_logo_picker(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.settings.edit'))
            ->assertOk()
            ->assertSee('name="site_logo"', false)
            ->assertSee('enctype="multipart/form-data"', false);
    }

    public function test_the_uploaded_logo_is_stored(): void
    {
        $name = $this->uploadLogo();

        $this->assertMatchesRegularExpression('/^logo-\d+-\d+\.png$/', $name);
        $this->assertFileExists(public_path('images/' . $name));
    }

    public function test_the_logo_reaches_the_public_site(): void
    {
        $name = $this->uploadLogo();
        $url = asset('images/' . $name);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('<link rel="icon" href="' . $url . '">', false)  // browser tab
            ->assertSee('class="brand-logo"', false);                    // site header
    }

    public function test_the_logo_reaches_the_homepage_footer(): void
    {
        $name = $this->uploadLogo();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('footer-logo', false)
            ->assertSee(asset('images/' . $name), false);
    }

    public function test_the_logo_reaches_the_dashboard(): void
    {
        $name = $this->uploadLogo();

        $this->actingAs($this->admin())
            ->get(route('admin.index'))
            ->assertOk()
            ->assertSee('<link rel="icon" href="' . asset('images/' . $name) . '">', false)
            ->assertSee('<img src="' . asset('images/' . $name) . '"', false);
    }

    public function test_the_site_falls_back_to_its_icon_without_a_logo(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('class="brand-logo"', false)   // the rule stays in the stylesheet
            ->assertSee('fa-mortar-pestle', false);
    }

    public function test_a_non_image_upload_is_rejected(): void
    {
        $this->saveSettings(['site_logo' => UploadedFile::fake()->create('notes.pdf', 100, 'application/pdf')])
            ->assertSessionHasErrors('site_logo');

        $this->assertEmpty(Setting::get('site_logo'));
    }

    public function test_the_logo_can_be_removed(): void
    {
        $name = $this->uploadLogo();

        $this->saveSettings(['remove_site_logo' => 1]);

        $this->assertEmpty(Setting::get('site_logo'));
        $this->assertFileDoesNotExist(public_path('images/' . $name));
    }

    public function test_replacing_the_logo_deletes_the_previous_file(): void
    {
        $first = $this->uploadLogo('first.png');
        $second = $this->uploadLogo('second.png');

        $this->assertNotSame($first, $second);
        $this->assertFileDoesNotExist(public_path('images/' . $first));
        $this->assertFileExists(public_path('images/' . $second));
    }

    public function test_a_missing_or_tampered_filename_is_ignored(): void
    {
        // A record left over from a deleted file must not show as a broken image
        Setting::put('site_logo', 'gone.png');
        $this->get(route('home'))->assertOk()->assertDontSee('gone.png', false);

        Setting::put('site_logo', '../../.env');
        $this->get(route('home'))->assertOk()->assertDontSee('.env', false);
    }

    public function test_only_an_admin_can_change_the_logo(): void
    {
        $customer = User::create([
            'name' => 'Sara', 'email' => 'sara@test.com',
            'password' => bcrypt('password'), 'type' => 'customer',
        ]);

        $this->actingAs($customer)
            ->put(route('admin.settings.update'), ['site_logo' => UploadedFile::fake()->image('logo.png')])
            ->assertRedirect('/');

        $this->assertEmpty(Setting::get('site_logo'));
    }
}
