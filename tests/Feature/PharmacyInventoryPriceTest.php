<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Pharmacy;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolesPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Tests\TestCase;

class PharmacyInventoryPriceTest extends TestCase
{
    use RefreshDatabase;

    public function test_saved_pharmacy_prices_and_visibility_survive_reloading_inventory(): void
    {
        $this->withoutMiddleware([LaravelLocalizationRedirectFilter::class, LocaleSessionRedirect::class]);
        $this->seed(RolesPermissionsSeeder::class);
        $pharmacy = Pharmacy::create(['name' => '{"en":"Branch A","ar":"A"}']);
        $other = Pharmacy::create(['name' => '{"en":"Branch B","ar":"B"}']);
        $user = User::create([
            'name' => 'Pharmacist', 'email' => 'price@test.com', 'password' => bcrypt('password'),
            'type' => 'admin', 'pharmacy_id' => $pharmacy->id,
            'role_id' => Role::where('slug', Role::PHARMACIST)->value('id'),
        ]);
        $category = Category::create(['name' => '{"en":"Medicines","ar":"Medicines"}', 'description' => '{}']);
        $product = Product::create([
            'name' => '{"en":"Medicine","ar":"Medicine"}', 'description' => '{}', 'price' => 15,
            'show_price' => true, 'quantity' => 30, 'category_id' => $category->id,
        ]);
        $other->products()->attach($product->id, ['quantity' => 8, 'price' => 77, 'show_price' => true]);

        foreach ([[22.75, 0], [0, 1], [31.5, 1]] as [$price, $visible]) {
            $this->actingAs($user)->from(route('admin.my-pharmacy.inventory'))
                ->post(route('admin.my-pharmacy.attach', $product), [
                    'quantity' => 5, 'price' => $price, 'show_price' => $visible,
                ])->assertRedirect(route('admin.my-pharmacy.inventory'))->assertSessionHasNoErrors();

            $this->assertDatabaseHas('pharmacy_product', [
                'pharmacy_id' => $pharmacy->id, 'product_id' => $product->id,
                'price' => $price, 'show_price' => $visible, 'quantity' => 5,
            ]);
            $response = $this->get(route('admin.my-pharmacy.inventory'))->assertOk()
                ->assertViewHas('prices', fn ($prices) => (float) $prices[$product->id] === (float) $price)
                ->assertViewHas('priceVisibility', fn ($values) => (bool) $values[$product->id] === (bool) $visible);
            $dom = new \DOMDocument();
            @$dom->loadHTML($response->getContent());
            $xpath = new \DOMXPath($dom);
            $this->assertEquals($price, $xpath->evaluate('string(//input[@name="price"]/@value)'));
            $this->assertSame($visible, $xpath->query('//input[@type="checkbox" and @name="show_price" and @checked]')->length);
        }

        $this->assertDatabaseHas('pharmacy_product', [
            'pharmacy_id' => $other->id, 'product_id' => $product->id, 'price' => 77, 'quantity' => 8,
        ]);
        $this->assertEquals(15, $product->fresh()->price);
    }
}
