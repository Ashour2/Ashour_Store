<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolesPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Tests\TestCase;

class CategoryEditingTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_editing_loads_scripts_and_preserves_data_and_replaces_image(): void
    {
        $this->withoutMiddleware([LaravelLocalizationRedirectFilter::class, LocaleSessionRedirect::class]);
        $this->seed(RolesPermissionsSeeder::class);
        $user = User::create(['name' => 'Admin', 'email' => 'category@test.com', 'password' => bcrypt('password'), 'type' => 'admin', 'role_id' => Role::where('slug', Role::SUPER_ADMIN)->value('id')]);
        $category = Category::create(['name' => '{"en":"Old","ar":"قديم"}', 'description' => '{}', 'home_display_order' => 3]);
        $this->actingAs($user)->get(route('admin.categories.edit', $category))->assertOk()
            ->assertSee('function showImg(e)', false)->assertSee('setupCategoryUnsavedWarning();', false)
            ->assertDontSee('back/dist/js/pages/dashboard.js', false);
        $data = ['name_en' => 'Updated', 'name_ar' => 'معدل', 'description_en' => 'Description', 'description_ar' => 'وصف', 'home_display_order' => 2];
        $paths = [];
        try {
            foreach (['first.png', 'second.png'] as $file) {
                $oldPath = $category->fresh()->image?->path;
                $this->put(route('admin.categories.update', $category), $data + ['image' => UploadedFile::fake()->image($file)])
                    ->assertSessionHasNoErrors()->assertRedirect(route('admin.categories.index'));
                $category->refresh();
                $paths[] = public_path('images/'.$category->image->path);
                $this->assertFileExists(end($paths));
                if ($oldPath) $this->assertFileDoesNotExist(public_path('images/'.$oldPath));
                $this->assertSame('Updated', $category->name_en);
                $this->assertSame('معدل', $category->name_ar);
                $this->assertEquals(2, $category->home_display_order);
            }
            $this->put(route('admin.categories.update', $category), $data)->assertSessionHasNoErrors();
            $this->assertSame(1, $category->image()->count());
        } finally {
            foreach ($paths as $path) if (is_file($path)) unlink($path);
        }
    }
}
