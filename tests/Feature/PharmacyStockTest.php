<?php

namespace Tests\Feature;

use App\Models\Pharmacy;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolesPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Tests\TestCase;

class PharmacyStockTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([LaravelLocalizationRedirectFilter::class, LocaleSessionRedirect::class]);
        $this->seed(RolesPermissionsSeeder::class);
    }

    protected function admin(): User
    {
        return User::create([
            'name' => 'Admin', 'email' => 'admin@test.com',
            'password' => bcrypt('password'), 'type' => 'admin',
            'role_id' => Role::where('slug', Role::SUPER_ADMIN)->value('id'),
        ]);
    }

    protected function customer(): User
    {
        return User::create([
            'name' => 'Sara', 'email' => 'sara@test.com',
            'password' => bcrypt('password'), 'type' => 'customer',
        ]);
    }

    protected function medicine(): Product
    {
        return Product::create([
            'name'        => json_encode(['en' => 'Panadol', 'ar' => 'بنادول'], JSON_UNESCAPED_UNICODE),
            'description' => json_encode(['en' => 'Pain relief', 'ar' => 'مسكن'], JSON_UNESCAPED_UNICODE),
            'price'       => 15, 'quantity' => 30, 'is_active' => true,
        ]);
    }

    protected function pharmacy(string $en, string $ar, array $attributes = []): Pharmacy
    {
        return Pharmacy::create($attributes + [
            'name' => json_encode(['en' => $en, 'ar' => $ar], JSON_UNESCAPED_UNICODE),
        ]);
    }

    public function test_the_stock_sheet_lists_every_pharmacy_with_its_quantity(): void
    {
        $medicine = $this->medicine();
        $downtown = $this->pharmacy('Downtown Branch', 'فرع الوسط');
        $north = $this->pharmacy('North Branch', 'فرع الشمال');

        $medicine->pharmacies()->attach($downtown->id, ['quantity' => 12]);

        $this->actingAs($this->admin())
            ->get(route('admin.products.stock.edit', $medicine->id))
            ->assertOk()
            ->assertSee('Panadol')
            ->assertSee('Downtown Branch')
            ->assertSee('North Branch')   // listed so it can be stocked in place
            ->assertSee('12');
    }

    public function test_quantities_are_saved_per_pharmacy(): void
    {
        $medicine = $this->medicine();
        $a = $this->pharmacy('Branch A', 'فرع أ');
        $b = $this->pharmacy('Branch B', 'فرع ب');

        $this->actingAs($this->admin())
            ->put(route('admin.products.stock.update', $medicine->id), [
                'quantities' => [$a->id => 7, $b->id => 3],
            ])
            ->assertRedirect(route('admin.products.stock.edit', $medicine->id));

        $medicine->refresh()->load('pharmacies');
        $this->assertSame(2, $medicine->pharmacies->count());
        $this->assertSame(7, $medicine->pharmacies->firstWhere('id', $a->id)->pivot->quantity);
        $this->assertSame(10, $medicine->branch_stock);
    }

    public function test_a_zero_or_empty_quantity_stops_stocking_that_branch(): void
    {
        $medicine = $this->medicine();
        $a = $this->pharmacy('Branch A', 'فرع أ');
        $b = $this->pharmacy('Branch B', 'فرع ب');
        $medicine->pharmacies()->attach([$a->id => ['quantity' => 5], $b->id => ['quantity' => 5]]);

        $this->actingAs($this->admin())
            ->put(route('admin.products.stock.update', $medicine->id), [
                'quantities' => [$a->id => 0, $b->id => ''],
            ]);

        $this->assertSame(0, $medicine->fresh()->pharmacies()->count());
    }

    public function test_an_unknown_pharmacy_id_is_ignored(): void
    {
        $medicine = $this->medicine();
        $a = $this->pharmacy('Branch A', 'فرع أ');

        $this->actingAs($this->admin())
            ->put(route('admin.products.stock.update', $medicine->id), [
                'quantities' => [$a->id => 4, 99999 => 50],
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame(1, $medicine->fresh()->pharmacies()->count());
    }

    public function test_a_negative_quantity_is_rejected(): void
    {
        $medicine = $this->medicine();
        $a = $this->pharmacy('Branch A', 'فرع أ');

        $this->actingAs($this->admin())
            ->put(route('admin.products.stock.update', $medicine->id), [
                'quantities' => [$a->id => -5],
            ])
            ->assertSessionHasErrors('quantities.' . $a->id);
    }

    public function test_deleting_a_pharmacy_clears_its_stock_rows(): void
    {
        $medicine = $this->medicine();
        $a = $this->pharmacy('Branch A', 'فرع أ');
        $medicine->pharmacies()->attach($a->id, ['quantity' => 9]);

        $this->actingAs($this->admin())->delete(route('admin.pharmacies.destroy', $a->id));

        $this->assertSame(0, $medicine->fresh()->pharmacies()->count());
    }

    public function test_a_pharmacy_can_be_created_with_a_bilingual_name(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.pharmacies.store'), [
                'name_en' => 'Central Pharmacy',
                'name_ar' => 'الصيدلية المركزية',
                'phone'   => '+966500000000',
                'address' => 'Riyadh',
            ])
            ->assertRedirect(route('admin.pharmacies.index'));

        $pharmacy = Pharmacy::first();
        $this->assertSame('Central Pharmacy', $pharmacy->name_en);
        $this->assertSame('الصيدلية المركزية', $pharmacy->name_ar);
        $this->assertFalse($pharmacy->is_active); // switch was not submitted
    }

    // --- Manager-only guarantees ---

    public function test_guests_cannot_reach_the_stock_sheet_or_pharmacies(): void
    {
        $medicine = $this->medicine();

        $this->get(route('admin.products.stock.edit', $medicine->id))->assertRedirect();
        $this->get(route('admin.pharmacies.index'))->assertRedirect();
        $this->put(route('admin.products.stock.update', $medicine->id), ['quantities' => []])->assertRedirect();
    }

    public function test_a_signed_in_customer_cannot_reach_the_stock_sheet(): void
    {
        $medicine = $this->medicine();

        // IsAdmin turns a non-admin away to the home page rather than 403-ing
        $customer = $this->customer();

        $this->actingAs($customer)
            ->get(route('admin.products.stock.edit', $medicine->id))
            ->assertRedirect('/');

        $this->actingAs($customer)
            ->get(route('admin.pharmacies.index'))
            ->assertRedirect('/');
    }

    public function test_stock_locations_never_appear_on_the_public_medicine_page(): void
    {
        $medicine = $this->medicine();
        $branch = $this->pharmacy('Secret Branch', 'الفرع السري');
        $medicine->pharmacies()->attach($branch->id, ['quantity' => 42]);

        $response = $this->get(route('medicines.show', $medicine->id));

        $response->assertOk()
            ->assertDontSee('Secret Branch')
            ->assertDontSee('الفرع السري')
            ->assertDontSee('42');
    }
}
