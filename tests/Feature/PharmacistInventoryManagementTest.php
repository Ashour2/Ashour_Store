<?php

namespace Tests\Feature;

use App\Models\{Category, Pharmacy, Product, Role, User};
use Database\Seeders\RolesPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mcamara\LaravelLocalization\Middleware\{LaravelLocalizationRedirectFilter, LocaleSessionRedirect};
use Tests\TestCase;

class PharmacistInventoryManagementTest extends TestCase
{
    use RefreshDatabase;

    private Pharmacy $branch;
    private User $pharmacist;
    private Product $medicine;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([LaravelLocalizationRedirectFilter::class, LocaleSessionRedirect::class]);
        $this->seed(RolesPermissionsSeeder::class);
        $this->branch = Pharmacy::create(['name' => '{"en":"My Branch","ar":"صيدليتي"}', 'is_active' => true, 'subscription_status' => 'active']);
        $this->pharmacist = User::create(['name' => 'Pharmacist', 'email' => 'inventory@test.com', 'password' => bcrypt('password'), 'role_id' => Role::where('slug', Role::PHARMACIST)->value('id'), 'pharmacy_id' => $this->branch->id]);
        $category = Category::create(['name' => '{"en":"Medicines","ar":"أدوية"}', 'description' => '{}']);
        $this->medicine = Product::create(['name' => '{"en":"Test medicine","ar":"دواء"}', 'description' => '{}', 'category_id' => $category->id, 'price' => 90, 'quantity' => 0, 'is_active' => true, 'stock_display' => 'quantity']);
    }

    public function test_adding_pharmacy_stock_makes_zero_catalogue_stock_orderable(): void
    {
        $this->actingAs($this->pharmacist)->post(route('admin.my-pharmacy.attach', $this->medicine), ['quantity' => 7, 'price' => 12.5, 'show_price' => 1])->assertSessionHasNoErrors();
        $this->assertTrue($this->medicine->fresh()->in_stock);
        $this->get(route('medicines.show', $this->medicine))->assertOk()->assertSee(__('site.in_stock'));
        $this->get(route('admin.index'))->assertOk()->assertViewHas('stats', fn ($stats) => $stats['out_of_stock'] === 0);
        $this->postJson(route('cart.add', $this->medicine), ['quantity' => 3])->assertOk()->assertJsonPath('count', 3);
    }

    public function test_edit_screen_saves_only_own_pharmacy_fields(): void
    {
        $other = Pharmacy::create(['name' => '{"en":"Other","ar":"أخرى"}']);
        $this->medicine->pharmacies()->attach([$this->branch->id => ['quantity' => 2, 'price' => 10], $other->id => ['quantity' => 9, 'price' => 40]]);
        $this->actingAs($this->pharmacist)->get(route('admin.products.edit', $this->medicine))->assertOk()->assertSee('name="is_available"', false);
        $this->put(route('admin.products.update', $this->medicine), ['price' => 15.75, 'quantity' => 6, 'is_available' => 1, 'show_price' => 0, 'pharmacy_notes' => 'Private', 'name_en' => 'Tampered', 'is_active' => 0, 'pharmacies' => [$other->id]])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('pharmacy_product', ['pharmacy_id' => $this->branch->id, 'product_id' => $this->medicine->id, 'price' => 15.75, 'quantity' => 6, 'show_price' => 0, 'notes' => 'Private']);
        $this->assertDatabaseHas('pharmacy_product', ['pharmacy_id' => $other->id, 'product_id' => $this->medicine->id, 'price' => 40, 'quantity' => 9]);
        $this->assertSame('Test medicine', $this->medicine->fresh()->name_en);
        $this->assertEquals(90, $this->medicine->fresh()->price);
        $this->assertTrue($this->medicine->fresh()->is_active);
    }

    public function test_unavailable_stock_cannot_be_ordered_and_can_be_restored_without_losing_quantity(): void
    {
        $this->branch->products()->attach($this->medicine->id, ['quantity' => 7]);
        $this->actingAs($this->pharmacist)->post(route('admin.my-pharmacy.attach', $this->medicine), ['quantity' => 7, 'price' => 13, 'is_available' => 0])->assertSessionHasNoErrors();
        $this->assertFalse($this->medicine->fresh()->in_stock);
        $this->postJson(route('cart.add', $this->medicine))->assertStatus(422);
        $this->post(route('admin.my-pharmacy.attach', $this->medicine), ['is_available' => 1])->assertSessionHasNoErrors();
        $this->assertTrue($this->medicine->fresh()->in_stock);
        $this->assertDatabaseHas('pharmacy_product', ['pharmacy_id' => $this->branch->id, 'product_id' => $this->medicine->id, 'quantity' => 7, 'price' => 13]);
    }

    public function test_blocked_and_foreign_inventory_cannot_be_edited(): void
    {
        $this->actingAs($this->pharmacist)->put(route('admin.products.update', $this->medicine), ['price' => 2])->assertForbidden();
        $this->branch->products()->attach($this->medicine->id, ['quantity' => 7, 'is_allowed' => false]);
        $this->put(route('admin.products.update', $this->medicine), ['price' => 2])->assertForbidden();
        $this->post(route('admin.my-pharmacy.attach', $this->medicine), ['quantity' => 7, 'price' => 2])->assertForbidden();
        $this->get(route('admin.products.edit', $this->medicine))->assertForbidden();
    }

    public function test_invalid_values_are_rejected_and_errors_are_visible(): void
    {
        $this->branch->products()->attach($this->medicine->id, ['quantity' => 7, 'price' => 10]);
        $this->actingAs($this->pharmacist)->from(route('admin.my-pharmacy.inventory'))
            ->post(route('admin.my-pharmacy.attach', $this->medicine), ['quantity' => -1, 'price' => -5])->assertSessionHasErrors(['quantity', 'price']);
        $this->get(route('admin.my-pharmacy.inventory'))->assertOk()->assertSee('alert-danger');
        $this->assertDatabaseHas('pharmacy_product', ['pharmacy_id' => $this->branch->id, 'product_id' => $this->medicine->id, 'quantity' => 7, 'price' => 10]);
    }

    public function test_toggle_and_bulk_availability_do_not_change_catalogue_or_other_pharmacies(): void
    {
        $other = Pharmacy::create(['name' => '{"en":"Other","ar":"أخرى"}']);
        $this->medicine->pharmacies()->attach([$this->branch->id => ['quantity' => 7], $other->id => ['quantity' => 9]]);
        $this->actingAs($this->pharmacist)->put(route('admin.products.toggle-active', $this->medicine))->assertRedirect();
        $this->assertDatabaseHas('pharmacy_product', ['pharmacy_id' => $this->branch->id, 'product_id' => $this->medicine->id, 'is_available' => false]);
        $this->put(route('admin.products.activate-all'))->assertRedirect();
        $this->assertDatabaseHas('pharmacy_product', ['pharmacy_id' => $this->branch->id, 'product_id' => $this->medicine->id, 'is_available' => true]);
        $this->put(route('admin.products.deactivate-all'))->assertRedirect();
        $this->assertTrue($this->medicine->fresh()->is_active);
        $this->assertDatabaseHas('pharmacy_product', ['pharmacy_id' => $other->id, 'product_id' => $this->medicine->id, 'is_available' => true]);
    }

    public function test_pharmacy_stock_controls_cart_limit_and_checkout_availability(): void
    {
        $this->medicine->update(['quantity' => 100]);
        $this->branch->products()->attach($this->medicine->id, ['quantity' => 3, 'price' => 12]);
        $this->postJson(route('cart.add', $this->medicine), ['quantity' => 10])->assertOk()->assertJsonPath('count', 3);
        $this->actingAs($this->pharmacist)->post(route('admin.my-pharmacy.attach', $this->medicine), ['is_available' => 0]);
        $this->post(route('cart.store'), ['customer_name' => 'Buyer', 'customer_phone' => '123456', 'pharmacy_id' => $this->branch->id])->assertSessionHasErrors('pharmacy_id');
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_new_medicine_is_linked_with_own_price_quantity_and_notes(): void
    {
        $this->actingAs($this->pharmacist)->post(route('admin.products.store'), [
            'name_en' => 'New medicine', 'name_ar' => 'دواء جديد', 'description_en' => 'Info', 'description_ar' => 'معلومات',
            'price' => 12, 'quantity' => 8, 'category_id' => $this->medicine->category_id, 'show_price' => 1, 'pharmacy_notes' => 'My notes',
        ])->assertSessionHasNoErrors();
        $product = Product::latest('id')->firstOrFail();
        $this->assertDatabaseHas('pharmacy_product', ['pharmacy_id' => $this->branch->id, 'product_id' => $product->id, 'quantity' => 8, 'price' => 12, 'notes' => 'My notes', 'is_available' => true]);
        $this->assertFalse($product->is_active);
        $this->get(route('admin.products.edit', $product))->assertOk()->assertSee('بانتظار تفعيل المدير');
    }

    public function test_removing_own_medicine_keeps_catalogue_and_other_branch_stock(): void
    {
        $other = Pharmacy::create(['name' => '{"en":"Other","ar":"أخرى"}']);
        $this->medicine->pharmacies()->attach([$this->branch->id => ['quantity' => 7], $other->id => ['quantity' => 9]]);
        $this->actingAs($this->pharmacist)->get(route('admin.delete_img', ['id' => 999]))->assertForbidden();
        $this->delete(route('admin.products.destroy', $this->medicine))->assertRedirect();
        $this->assertModelExists($this->medicine);
        $this->assertDatabaseMissing('pharmacy_product', ['pharmacy_id' => $this->branch->id, 'product_id' => $this->medicine->id]);
        $this->assertDatabaseHas('pharmacy_product', ['pharmacy_id' => $other->id, 'product_id' => $this->medicine->id, 'quantity' => 9]);
    }
}
