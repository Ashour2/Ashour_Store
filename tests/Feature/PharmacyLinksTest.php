<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Image;
use App\Models\Pharmacy;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolesPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Tests\TestCase;

class PharmacyLinksTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([LaravelLocalizationRedirectFilter::class, LocaleSessionRedirect::class]);
        $this->seed(RolesPermissionsSeeder::class);
    }

    /**
     * Creating a medicine writes its image into public/images, so clear out
     * anything this test uploaded instead of letting it pile up there.
     */
    protected function tearDown(): void
    {
        foreach (Image::pluck('path') as $path) {
            File::delete(public_path('images/' . $path));
        }

        parent::tearDown();
    }

    protected function admin(): User
    {
        return User::create([
            'name' => 'Admin', 'email' => 'admin@test.com',
            'password' => bcrypt('password'), 'type' => 'admin',
            'role_id' => Role::where('slug', Role::SUPER_ADMIN)->value('id'),
        ]);
    }

    protected function category(): Category
    {
        return Category::create([
            'name'        => json_encode(['en' => 'Painkillers', 'ar' => 'مسكنات'], JSON_UNESCAPED_UNICODE),
            'description' => json_encode(['en' => 'x', 'ar' => 'س'], JSON_UNESCAPED_UNICODE),
        ]);
    }

    protected function medicine(string $en = 'Panadol', string $ar = 'بنادول'): Product
    {
        return Product::create([
            'name'        => json_encode(['en' => $en, 'ar' => $ar], JSON_UNESCAPED_UNICODE),
            'description' => json_encode(['en' => 'Pain relief', 'ar' => 'مسكن'], JSON_UNESCAPED_UNICODE),
            'price'       => 15, 'quantity' => 30, 'category_id' => $this->category()->id,
        ]);
    }

    protected function pharmacy(string $en, string $ar): Pharmacy
    {
        return Pharmacy::create([
            'name' => json_encode(['en' => $en, 'ar' => $ar], JSON_UNESCAPED_UNICODE),
        ]);
    }

    // --- 1. Clicking the medicines count opens that pharmacy's medicines ---

    public function test_a_pharmacy_page_lists_the_medicines_it_holds_with_quantities(): void
    {
        $branch = $this->pharmacy('North Branch', 'فرع الشمال');
        $held = $this->medicine('Panadol', 'بنادول');
        $this->medicine('Aspirin', 'أسبرين');

        $held->pharmacies()->attach($branch->id, ['quantity' => 17]);

        $this->actingAs($this->admin())
            ->get(route('admin.pharmacies.show', $branch->id))
            ->assertOk()
            ->assertSee('North Branch')
            ->assertSee('Panadol')
            ->assertSee('17')
            ->assertDontSee('Aspirin');   // not stocked here
    }

    public function test_the_pharmacies_list_links_each_count_to_its_page(): void
    {
        $branch = $this->pharmacy('North Branch', 'فرع الشمال');
        $this->medicine()->pharmacies()->attach($branch->id, ['quantity' => 4]);

        $this->actingAs($this->admin())
            ->get(route('admin.pharmacies.index'))
            ->assertOk()
            ->assertSee(route('admin.pharmacies.show', $branch->id), false);
    }

    // --- 2. Creating a medicine assigns it to pharmacies ---

    public function test_the_medicine_form_offers_every_pharmacy_and_an_add_button(): void
    {
        $this->pharmacy('North Branch', 'فرع الشمال');

        $this->actingAs($this->admin())
            ->get(route('admin.products.create'))
            ->assertOk()
            ->assertSee('North Branch')
            ->assertSee('quantities[', false)
            ->assertSee('name="pharmacies[]"', false)
            ->assertSee('addPharmacyModal', false);
    }

    public function test_creating_a_medicine_stores_its_pharmacy_quantities(): void
    {
        $category = $this->category();
        $a = $this->pharmacy('Branch A', 'فرع أ');
        $b = $this->pharmacy('Branch B', 'فرع ب');

        $this->actingAs($this->admin())->post(route('admin.products.store'), [
            'name_en' => 'Panadol', 'name_ar' => 'بنادول',
            'description_en' => 'Pain', 'description_ar' => 'ألم',
            'price' => 10, 'quantity' => 25, 'category_id' => $category->id,
            'image' => UploadedFile::fake()->image('med.png'),
            'pharmacies' => [$a->id],
            'quantities' => [$a->id => 15, $b->id => 9],
        ])->assertRedirect(route('admin.products.index'));

        $product = Product::first();
        $this->assertNotNull($product);
        // Only the ticked branch is stocked, even though B had a quantity typed
        $this->assertSame(1, $product->pharmacies()->count());
        $this->assertSame(15, $product->pharmacies()->first()->pivot->quantity);
    }

    public function test_editing_a_medicine_updates_its_pharmacy_quantities(): void
    {
        $medicine = $this->medicine();
        $a = $this->pharmacy('Branch A', 'فرع أ');
        $medicine->pharmacies()->attach($a->id, ['quantity' => 5]);

        $this->actingAs($this->admin())->put(route('admin.products.update', $medicine->id), [
            'name_en' => 'Panadol', 'name_ar' => 'بنادول',
            'description_en' => 'Pain', 'description_ar' => 'ألم',
            'price' => 10, 'quantity' => 25, 'category_id' => $medicine->category_id,
            'pharmacies' => [$a->id],
            'quantities' => [$a->id => 40],
        ])->assertRedirect();

        $this->assertSame(40, $medicine->fresh()->pharmacies()->first()->pivot->quantity);
    }

    public function test_a_pharmacy_can_be_created_inline_as_json(): void
    {
        $response = $this->actingAs($this->admin())
            ->postJson(route('admin.pharmacies.store'), [
                'name_en' => 'Quick Branch',
                'name_ar' => 'فرع سريع',
                'phone'   => '+966500000000',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('name', 'Quick Branch')
            ->assertJsonStructure(['id', 'name', 'phone', 'address', 'message']);

        $this->assertSame(1, Pharmacy::count());
    }

    public function test_an_invalid_inline_pharmacy_returns_validation_errors_as_json(): void
    {
        $this->actingAs($this->admin())
            ->postJson(route('admin.pharmacies.store'), ['name_en' => '', 'name_ar' => ''])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name_en', 'name_ar']);

        $this->assertSame(0, Pharmacy::count());
    }

    public function test_a_ticked_branch_with_no_quantity_is_stocked_at_zero(): void
    {
        $medicine = $this->medicine();
        $a = $this->pharmacy('Branch A', 'فرع أ');

        // A branch that carries the medicine but has run out
        $this->actingAs($this->admin())->put(route('admin.products.update', $medicine->id), [
            'name_en' => 'Panadol', 'name_ar' => 'بنادول',
            'description_en' => 'Pain', 'description_ar' => 'ألم',
            'price' => 10, 'quantity' => 25, 'category_id' => $medicine->category_id,
            'pharmacies' => [$a->id],
            'quantities' => [$a->id => ''],
        ])->assertRedirect();

        $this->assertSame(1, $medicine->fresh()->pharmacies()->count());
        $this->assertSame(0, $medicine->fresh()->pharmacies()->first()->pivot->quantity);
    }

    public function test_unticking_every_branch_clears_the_distribution(): void
    {
        $medicine = $this->medicine();
        $a = $this->pharmacy('Branch A', 'فرع أ');
        $medicine->pharmacies()->attach($a->id, ['quantity' => 12]);

        $this->actingAs($this->admin())->put(route('admin.products.update', $medicine->id), [
            'name_en' => 'Panadol', 'name_ar' => 'بنادول',
            'description_en' => 'Pain', 'description_ar' => 'ألم',
            'price' => 10, 'quantity' => 25, 'category_id' => $medicine->category_id,
        ])->assertRedirect();

        $this->assertSame(0, $medicine->fresh()->pharmacies()->count());
    }

    public function test_the_edit_form_pre_ticks_the_branches_already_stocking_it(): void
    {
        $medicine = $this->medicine();
        $stocked = $this->pharmacy('Stocked Branch', 'فرع يملكه');
        $other = $this->pharmacy('Other Branch', 'فرع آخر');
        $medicine->pharmacies()->attach($stocked->id, ['quantity' => 8]);

        $html = $this->actingAs($this->admin())
            ->get(route('admin.products.edit', $medicine->id))
            ->assertOk()
            ->getContent();

        $this->assertMatchesRegularExpression(
            '/value="' . $stocked->id . '"[^>]*checked/',
            $html,
            'the stocked branch should be ticked'
        );
        $this->assertMatchesRegularExpression(
            '/id="qty' . $other->id . '"[^>]*disabled/',
            $html,
            'an unticked branch keeps its quantity box disabled'
        );
    }

    public function test_every_branch_stays_in_the_form_even_when_paged(): void
    {
        // Paging is done in the browser, so all branches must reach the page;
        // otherwise a branch ticked on page 2 would be dropped on submit.
        for ($i = 1; $i <= 12; $i++) {
            $this->pharmacy('Branch ' . $i, 'فرع ' . $i);
        }

        $html = $this->actingAs($this->admin())
            ->get(route('admin.products.create'))
            ->assertOk()
            ->assertSee('Branch 1')
            ->assertSee('Branch 12')   // last page, still rendered
            ->assertSee('pharmacyPager', false)
            ->assertSee('pharmacySearch', false)
            ->getContent();

        $this->assertSame(12, preg_match_all('/name="pharmacies\[\]"/', $html));
    }

    public function test_a_branch_from_a_later_page_is_saved(): void
    {
        $medicine = $this->medicine();
        $branches = [];
        for ($i = 1; $i <= 12; $i++) {
            $branches[] = $this->pharmacy('Branch ' . $i, 'فرع ' . $i);
        }
        $eleventh = $branches[10];

        $this->actingAs($this->admin())->put(route('admin.products.update', $medicine->id), [
            'name_en' => 'Panadol', 'name_ar' => 'بنادول',
            'description_en' => 'Pain', 'description_ar' => 'ألم',
            'price' => 10, 'quantity' => 25, 'category_id' => $medicine->category_id,
            'pharmacies' => [$eleventh->id],
            'quantities' => [$eleventh->id => 33],
        ])->assertRedirect();

        $stocked = $medicine->fresh()->pharmacies;
        $this->assertSame(1, $stocked->count());
        $this->assertSame($eleventh->id, $stocked->first()->id);
        $this->assertSame(33, $stocked->first()->pivot->quantity);
    }

    public function test_the_branch_list_can_be_folded_away(): void
    {
        $this->pharmacy('North Branch', 'فرع الشمال');

        $this->actingAs($this->admin())
            ->get(route('admin.products.create'))
            ->assertOk()
            ->assertSee('togglePharmacies', false)
            ->assertSee('data-target="#pharmacyBox"', false)
            ->assertSee('pharmacy-scroll', false);
    }

    // --- 3. The medicines list shows which pharmacies stock each medicine ---

    public function test_the_medicines_list_shows_the_pharmacies_column(): void
    {
        $medicine = $this->medicine();
        $branch = $this->pharmacy('North Branch', 'فرع الشمال');
        $medicine->pharmacies()->attach($branch->id, ['quantity' => 9]);

        $this->actingAs($this->admin())
            ->get(route('admin.products.index'))
            ->assertOk()
            ->assertSee('North Branch')          // inside the breakdown modal
            ->assertSee('9')
            ->assertSee('phModal' . $medicine->id, false);
    }

    public function test_a_medicine_held_nowhere_shows_no_modal(): void
    {
        $medicine = $this->medicine();

        $this->actingAs($this->admin())
            ->get(route('admin.products.index'))
            ->assertOk()
            ->assertDontSee('phModal' . $medicine->id, false);
    }
}
