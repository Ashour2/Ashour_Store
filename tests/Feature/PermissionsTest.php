<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Pharmacy;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolesPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Tests\TestCase;

class PermissionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([LaravelLocalizationRedirectFilter::class, LocaleSessionRedirect::class]);
        $this->seed(RolesPermissionsSeeder::class);
    }

    protected function userWithRole(string $slug, string $email = null): User
    {
        $pharmacyId = null;
        if ($slug === Role::PHARMACIST) {
            $pharmacyId = Pharmacy::create([
                'name' => json_encode(['en' => 'Test Pharmacy', 'ar' => 'صيدلية اختبار'], JSON_UNESCAPED_UNICODE),
            ])->id;
        }

        return User::create([
            'name'     => ucfirst($slug),
            'email'    => $email ?: $slug . '@test.com',
            'password' => bcrypt('password'),
            'type'     => 'employee',
            'role_id'  => Role::where('slug', $slug)->value('id'),
            'pharmacy_id' => $pharmacyId,
        ]);
    }

    // --- The catalogue ---

    public function test_every_dashboard_feature_has_permissions(): void
    {
        $groups = Permission::pluck('group')->unique()->values()->all();

        foreach (['dashboard', 'categories', 'products', 'pharmacies', 'articles',
                  'consultations', 'orders', 'customers', 'settings', 'users'] as $expected) {
            $this->assertContains($expected, $groups, "missing permissions for $expected");
        }

        $this->assertSame(34, Permission::count());
    }

    public function test_the_four_roles_are_seeded(): void
    {
        $this->assertSame(4, Role::count());

        $owner = Role::where('slug', Role::SUPER_ADMIN)->first();
        $this->assertTrue($owner->is_locked);
        $this->assertSame(Permission::count(), $owner->permissions()->count());

        $admin = Role::where('slug', Role::ADMIN)->first();
        $this->assertSame(Permission::count(), $admin->permissions()->count());

        // The pharmacist starts on a subset the owner can widen
        $this->assertLessThan(Permission::count(), Role::where('slug', Role::PHARMACIST)->first()->permissions()->count());
    }

    // --- The owner ---

    public function test_the_owner_holds_every_permission(): void
    {
        $owner = $this->userWithRole(Role::SUPER_ADMIN);

        $this->assertTrue($owner->isSuperAdmin());
        $this->assertTrue($owner->hasPermission('settings.manage'));
        // Even an ability nobody was granted
        $this->assertTrue($owner->hasPermission('anything.at.all'));
    }

    public function test_the_owners_permissions_cannot_be_edited(): void
    {
        $owner = Role::where('slug', Role::SUPER_ADMIN)->first();

        $this->actingAs($this->userWithRole(Role::SUPER_ADMIN))
            ->get(route('admin.roles.edit', $owner->id))
            ->assertStatus(403);

        $this->actingAs($this->userWithRole(Role::SUPER_ADMIN, 'other@test.com'))
            ->put(route('admin.roles.update', $owner->id), ['permissions' => []])
            ->assertStatus(403);

        $this->assertSame(Permission::count(), $owner->fresh()->permissions()->count());
    }

    // --- The pharmacist is really limited ---

    public function test_pharmacist_can_edit_categories_but_cannot_delete_them(): void
    {
        $user = $this->userWithRole(Role::PHARMACIST);
        $category = \App\Models\Category::create(['name' => '{"en":"Original","ar":"قديم"}', 'description' => '{}']);
        $this->actingAs($user)->get(route('admin.categories.index'))->assertOk()
            ->assertSee(route('admin.categories.edit', $category), false)
            ->assertDontSee('name="_method" value="delete"', false);
        $this->get(route('admin.categories.edit', $category))->assertOk();
        $this->put(route('admin.categories.update', $category), ['name_en' => 'Updated', 'name_ar' => 'معدل', 'home_display_order' => 2])
            ->assertSessionHasNoErrors()->assertRedirect(route('admin.categories.index'));
        $this->assertSame('Updated', $category->fresh()->name_en);
        $this->delete(route('admin.categories.destroy', $category))->assertForbidden();
        $this->assertModelExists($category);
    }

    public function test_migration_grants_category_edit_to_existing_pharmacist_role(): void
    {
        $role = Role::where('slug', Role::PHARMACIST)->firstOrFail();
        $ids = Permission::whereIn('code', ['categories.view', 'categories.edit'])->pluck('id');
        $role->permissions()->detach($ids);
        $migration = require database_path('migrations/2026_10_24_120000_grant_pharmacist_category_edit_permission.php');
        $migration->up();
        $migration->up();
        $this->assertSame(2, $role->permissions()->whereIn('permissions.id', $ids)->count());
    }

    public function test_a_pharmacist_reaches_only_what_its_role_allows(): void
    {
        $pharmacist = $this->userWithRole(Role::PHARMACIST);

        // Granted by the starter set
        $this->actingAs($pharmacist)->get(route('admin.index'))->assertOk();
        $this->actingAs($pharmacist)->get(route('admin.products.index'))->assertOk();
        $this->actingAs($pharmacist)->get(route('admin.products.create'))->assertOk();

        $product = Product::create([
            'name' => json_encode(['en' => 'Test medicine', 'ar' => 'دواء تجريبي'], JSON_UNESCAPED_UNICODE),
            'description' => json_encode(['en' => 'x', 'ar' => 'س'], JSON_UNESCAPED_UNICODE),
            'price' => 5,
            'quantity' => 1,
        ]);

        // Not granted
        $this->actingAs($pharmacist)->get(route('admin.settings.edit'))->assertStatus(403);
        $this->actingAs($pharmacist)->get(route('admin.consultations.index'))->assertStatus(403);
        $this->actingAs($pharmacist)->get(route('admin.users.index'))->assertStatus(403);
        $this->actingAs($pharmacist)->get(route('admin.roles.index'))->assertStatus(403);
        $this->actingAs($pharmacist)->get(route('admin.products.stock.edit', $product))->assertStatus(403);
    }

    public function test_a_pharmacist_cannot_delete_a_medicine_from_another_pharmacy(): void
    {
        $pharmacist = $this->userWithRole(Role::PHARMACIST);
        $otherPharmacy = Pharmacy::create([
            'name' => json_encode(['en' => 'Other Pharmacy', 'ar' => 'صيدلية أخرى'], JSON_UNESCAPED_UNICODE),
        ]);
        $product = Product::create([
            'name'        => json_encode(['en' => 'Panadol', 'ar' => 'بنادول'], JSON_UNESCAPED_UNICODE),
            'description' => json_encode(['en' => 'x', 'ar' => 'س'], JSON_UNESCAPED_UNICODE),
            'price' => 5, 'quantity' => 1,
        ]);
        $product->pharmacies()->attach($otherPharmacy->id, ['quantity' => 1]);

        $this->actingAs($pharmacist)
            ->delete(route('admin.products.destroy', $product->id))
            ->assertStatus(403);

        $this->assertSame(1, Product::count());
    }

    public function test_granting_a_permission_opens_the_page_immediately(): void
    {
        $pharmacist = $this->userWithRole(Role::PHARMACIST);
        $role = Role::where('slug', Role::PHARMACIST)->first();

        $this->actingAs($pharmacist)->get(route('admin.settings.edit'))->assertStatus(403);

        $role->permissions()->attach(Permission::where('code', 'settings.manage')->value('id'));

        $this->actingAs($pharmacist->fresh())->get(route('admin.settings.edit'))->assertOk();
    }

    public function test_the_sidebar_hides_what_the_user_cannot_reach(): void
    {
        $html = $this->actingAs($this->userWithRole(Role::PHARMACIST))
            ->get(route('admin.index'))
            ->assertOk()
            ->getContent();

        $this->assertStringNotContainsString(route('admin.settings.edit'), $html);
        $this->assertStringNotContainsString(route('admin.users.index'), $html);
        $this->assertStringContainsString(route('admin.products.index'), $html);
    }

    // --- Non-staff ---

    public function test_a_user_without_a_role_cannot_open_the_dashboard(): void
    {
        $customer = User::create([
            'name' => 'Sara', 'email' => 'sara@test.com',
            'password' => bcrypt('password'), 'type' => 'customer',
        ]);

        $this->actingAs($customer)->get(route('admin.index'))->assertRedirect('/');
        $this->assertFalse($customer->hasPermission('products.view'));
    }

    // --- Managing staff ---

    public function test_an_admin_can_create_a_pharmacist(): void
    {
        $pharmacy = Pharmacy::create([
            'name' => json_encode(['en' => 'Main Pharmacy', 'ar' => 'الصيدلية الرئيسية'], JSON_UNESCAPED_UNICODE),
        ]);
        $this->actingAs($this->userWithRole(Role::ADMIN))
            ->post(route('admin.users.store'), [
                'name' => 'New Pharmacist',
                'email' => 'new@test.com',
                'password' => 'secret123',
                'password_confirmation' => 'secret123',
                'role_id' => Role::where('slug', Role::PHARMACIST)->value('id'),
                'pharmacy_id' => $pharmacy->id,
            ])
            ->assertRedirect(route('admin.users.index'));

        $created = User::where('email', 'new@test.com')->first();
        $this->assertNotNull($created);
        $this->assertSame(Role::PHARMACIST, $created->role->slug);
        $this->assertSame($pharmacy->id, $created->pharmacy_id);
    }

    public function test_the_owner_role_cannot_be_handed_out_from_the_users_screen(): void
    {
        $this->actingAs($this->userWithRole(Role::ADMIN))
            ->post(route('admin.users.store'), [
                'name' => 'Sneaky',
                'email' => 'sneaky@test.com',
                'password' => 'secret123',
                'password_confirmation' => 'secret123',
                'role_id' => Role::where('slug', Role::SUPER_ADMIN)->value('id'),
            ])
            ->assertSessionHasErrors('role_id');

        $this->assertNull(User::where('email', 'sneaky@test.com')->first());
    }

    public function test_the_owner_account_cannot_be_edited_from_the_users_screen(): void
    {
        $owner = $this->userWithRole(Role::SUPER_ADMIN);
        $admin = $this->userWithRole(Role::ADMIN);

        $this->actingAs($admin)->get(route('admin.users.edit', $owner->id))->assertStatus(403);
        $this->actingAs($admin)->delete(route('admin.users.destroy', $owner->id))->assertStatus(403);

        $this->assertNotNull(User::find($owner->id));
    }

    public function test_editing_a_user_without_a_password_keeps_the_old_one(): void
    {
        $admin = $this->userWithRole(Role::ADMIN);
        $pharmacist = $this->userWithRole(Role::PHARMACIST);
        $before = $pharmacist->password;

        $this->actingAs($admin)->put(route('admin.users.update', $pharmacist->id), [
            'name' => 'Renamed',
            'email' => $pharmacist->email,
            'role_id' => $pharmacist->role_id,
            'pharmacy_id' => $pharmacist->pharmacy_id,
            'password' => '',
            'password_confirmation' => '',
        ])->assertRedirect(route('admin.users.index'));

        $pharmacist->refresh();
        $this->assertSame('Renamed', $pharmacist->name);
        $this->assertSame($before, $pharmacist->password);
    }

    public function test_a_role_permission_matrix_saves_what_is_ticked(): void
    {
        $role = Role::where('slug', Role::PHARMACIST)->first();
        $codes = ['orders.view', 'products.view'];
        $ids = Permission::whereIn('code', $codes)->pluck('id')->all();

        $this->actingAs($this->userWithRole(Role::SUPER_ADMIN))
            ->put(route('admin.roles.update', $role->id), ['permissions' => $ids])
            ->assertRedirect(route('admin.roles.index'));

        $this->assertSame($codes, $role->fresh()->permissions()->pluck('code')->sort()->values()->all());
    }

    public function test_an_invented_permission_id_is_ignored(): void
    {
        $role = Role::where('slug', Role::PHARMACIST)->first();
        $real = Permission::where('code', 'products.view')->value('id');

        $this->actingAs($this->userWithRole(Role::SUPER_ADMIN))
            ->put(route('admin.roles.update', $role->id), ['permissions' => [$real, 999999]]);

        $this->assertSame(1, $role->fresh()->permissions()->count());
    }

    // --- The account page ---

    public function test_a_manager_can_change_their_email_and_password(): void
    {
        $owner = $this->userWithRole(Role::SUPER_ADMIN);

        $this->actingAs($owner)->put(route('admin.profile_data'), [
            'name' => 'Owner Renamed',
            'email' => 'owner-new@test.com',
            'current_password' => 'password',
            'password' => 'newsecret123',
            'password_confirmation' => 'newsecret123',
        ])->assertRedirect();

        $owner->refresh();
        $this->assertSame('owner-new@test.com', $owner->email);
        $this->assertSame('Owner Renamed', $owner->name);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('newsecret123', $owner->password));
    }

    public function test_the_account_page_rejects_a_wrong_current_password(): void
    {
        $owner = $this->userWithRole(Role::SUPER_ADMIN);

        $this->actingAs($owner)->put(route('admin.profile_data'), [
            'name' => 'Nope',
            'email' => 'nope@test.com',
            'current_password' => 'wrong-password',
        ])->assertSessionHasErrors('current_password');

        $this->assertSame('super_admin@test.com', $owner->fresh()->email);
    }

    public function test_the_account_page_is_open_to_any_staff_member(): void
    {
        // No permission code guards your own account
        $this->actingAs($this->userWithRole(Role::PHARMACIST))
            ->get(route('admin.profile'))
            ->assertOk();
    }
}
