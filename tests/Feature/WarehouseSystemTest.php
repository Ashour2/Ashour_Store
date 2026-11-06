<?php

namespace Tests\Feature;

use App\Models\{Category, Order, Pharmacy, Product, Role, User, Warehouse, WarehouseStock, WholesaleOrder};
use Database\Seeders\RolesPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Mcamara\LaravelLocalization\Middleware\{LaravelLocalizationRedirectFilter, LocaleSessionRedirect};
use Tests\TestCase;

class WarehouseSystemTest extends TestCase
{
    use RefreshDatabase;

    private Warehouse $warehouse;
    private Warehouse $otherWarehouse;
    private User $manager;
    private User $otherManager;
    private User $pharmacist;
    private WarehouseStock $stock;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([LaravelLocalizationRedirectFilter::class, LocaleSessionRedirect::class]);
        $this->seed(RolesPermissionsSeeder::class);
        $this->warehouse = Warehouse::create(['name' => 'Warehouse A', 'phone' => '0590000000']);
        $this->otherWarehouse = Warehouse::create(['name' => 'Warehouse B']);
        $this->manager = $this->user(Role::WAREHOUSE, ['warehouse_id' => $this->warehouse->id]);
        $this->otherManager = $this->user(Role::WAREHOUSE, ['warehouse_id' => $this->otherWarehouse->id]);
        $pharmacy = Pharmacy::create(['name' => '{"en":"Buyer pharmacy","ar":"صيدلية المشترى"}', 'is_active' => true, 'subscription_status' => 'active', 'address' => 'Hebron']);
        $this->pharmacist = $this->user(Role::PHARMACIST, ['pharmacy_id' => $pharmacy->id]);
        $category = Category::create(['name' => '{"en":"Medicines","ar":"أدوية"}', 'description' => '{}']);
        $this->product = Product::create(['name' => '{"en":"Wholesale medicine","ar":"دواء المستودع"}', 'description' => '{}', 'category_id' => $category->id, 'price' => 50, 'quantity' => 0, 'is_active' => true]);
        $this->stock = WarehouseStock::create(['warehouse_id' => $this->warehouse->id, 'product_id' => $this->product->id, 'quantity' => 10, 'price' => 12.5, 'is_available' => true]);
    }

    private function user(string $slug, array $attributes = []): User
    {
        return User::create($attributes + ['name' => $slug, 'email' => Str::uuid().'@test.com', 'password' => bcrypt('password'), 'type' => 'employee', 'role_id' => Role::where('slug', $slug)->firstOrFail()->id]);
    }

    private function payload(array $overrides = []): array
    {
        return $overrides + ['quantities' => [$this->stock->id => 3], 'request_token' => (string) Str::uuid(), 'delivery_address' => 'Hebron street', 'notes' => 'Call first'];
    }

    private function place(): WholesaleOrder
    {
        $this->actingAs($this->pharmacist)->post(route('admin.wholesale.store', $this->warehouse), $this->payload())->assertSessionHasNoErrors()->assertRedirect();
        return WholesaleOrder::latest('id')->firstOrFail();
    }

    public function test_admin_creates_warehouse_and_dedicated_account(): void
    {
        $admin = $this->user(Role::SUPER_ADMIN);
        $this->actingAs($admin)->get(route('admin.warehouses.manage'))->assertOk();
        $this->post(route('admin.warehouses.store'), ['name' => 'New warehouse', 'manager_name' => 'Owner', 'email' => 'new-warehouse@test.com', 'password' => 'password123', 'password_confirmation' => 'password123'])->assertSessionHasNoErrors();
        $user = User::where('email', 'new-warehouse@test.com')->firstOrFail();
        $this->assertTrue($user->isWarehouse());
        $this->assertSame('New warehouse', $user->warehouse->name);
        $this->assertNull($user->pharmacy_id);
        $this->actingAs($user)->get(route('admin.index'))->assertRedirect(route('admin.warehouse.inventory'));
    }

    public function test_warehouse_account_can_sign_in_and_guests_cannot_browse_wholesale(): void
    {
        $this->get(route('admin.wholesale.catalog'))->assertRedirect();
        $this->post(route('login'), ['email' => $this->manager->email, 'password' => 'password'])->assertRedirect();
        $this->assertAuthenticatedAs($this->manager);
        $this->get(route('admin.index'))->assertRedirect(route('admin.warehouse.inventory'));
        $this->get(route('admin.warehouse.inventory'))->assertOk();
    }

    public function test_warehouse_has_separate_inventory_and_cannot_access_pharmacy_features(): void
    {
        $this->actingAs($this->manager)->get(route('admin.warehouse.inventory'))->assertOk()->assertSee('Wholesale medicine');
        $this->post(route('admin.warehouse.inventory.save', $this->product), ['quantity' => 8, 'price' => 9.25, 'is_available' => 1, 'warehouse_id' => $this->otherWarehouse->id])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('warehouse_stocks', ['warehouse_id' => $this->warehouse->id, 'product_id' => $this->product->id, 'quantity' => 8, 'price' => 9.25]);
        $this->assertDatabaseMissing('warehouse_stocks', ['warehouse_id' => $this->otherWarehouse->id]);
        $this->assertDatabaseCount('pharmacy_product', 0);
        $this->assertFalse($this->product->fresh()->in_stock);
        foreach (['admin.products.index', 'admin.orders.index', 'admin.users.index', 'admin.warehouses.manage', 'admin.wholesale.catalog', 'admin.my-pharmacy.inventory'] as $route) $this->get(route($route))->assertForbidden();
        $this->actingAs($this->pharmacist)->get(route('admin.warehouse.inventory'))->assertForbidden();
    }

    public function test_pharmacy_sees_catalogue_and_submits_a_separate_order_at_server_prices(): void
    {
        $this->actingAs($this->pharmacist)->get(route('admin.wholesale.catalog'))->assertOk()->assertSee('Warehouse A');
        $this->get(route('admin.wholesale.warehouse', $this->warehouse))->assertOk()->assertSee('12.50');
        $payload = $this->payload(['price' => 0.01, 'total' => 0.01, 'pharmacy_id' => 999]);
        $this->post(route('admin.wholesale.store', $this->warehouse), $payload)->assertSessionHasNoErrors();
        $order = WholesaleOrder::firstOrFail();
        $this->assertEquals(37.5, $order->total);
        $this->assertSame($this->pharmacist->pharmacy_id, $order->pharmacy_id);
        $this->assertSame(7, $this->stock->fresh()->quantity);
        $this->assertSame(0, Order::count());
        $this->get(route('admin.wholesale.orders.show', $order))->assertOk()->assertSee('الدفع والتسوية خارج المنصة.');
        $this->actingAs($this->manager)->get(route('admin.wholesale.orders'))->assertOk()->assertSee($order->number);
        $this->get(route('admin.wholesale.orders.show', $order))->assertOk();
        $this->assertDatabaseHas('admin_notifications', ['user_id' => $this->manager->id, 'type' => 'wholesale']);
    }

    public function test_order_is_idempotent_and_snapshots_the_original_price(): void
    {
        $data = $this->payload();
        $this->actingAs($this->pharmacist)->post(route('admin.wholesale.store', $this->warehouse), $data)->assertSessionHasNoErrors();
        $this->stock->update(['price' => 90]);
        $this->post(route('admin.wholesale.store', $this->warehouse), $data)->assertSessionHasNoErrors();
        $this->assertSame(1, WholesaleOrder::count());
        $this->assertSame(7, $this->stock->fresh()->quantity);
        $this->assertEquals(12.5, WholesaleOrder::firstOrFail()->items()->firstOrFail()->price);
    }

    public function test_insufficient_or_foreign_stock_rejects_the_whole_order(): void
    {
        $otherStock = WarehouseStock::create(['warehouse_id' => $this->otherWarehouse->id, 'product_id' => $this->product->id, 'price' => 1, 'quantity' => 100]);
        foreach ([[$this->stock->id => 11], [$this->stock->id => 2, $otherStock->id => 1], [$this->stock->id => 0], [$this->stock->id => -1]] as $quantities) {
            $this->actingAs($this->pharmacist)->post(route('admin.wholesale.store', $this->warehouse), $this->payload(['quantities' => $quantities]))->assertSessionHasErrors();
        }
        $this->assertSame(0, WholesaleOrder::count());
        $this->assertSame(10, $this->stock->fresh()->quantity);
    }

    public function test_reserved_stock_cannot_be_sold_twice(): void
    {
        $this->actingAs($this->pharmacist)->post(route('admin.wholesale.store', $this->warehouse), $this->payload(['quantities' => [$this->stock->id => 8]]))->assertSessionHasNoErrors();
        $this->post(route('admin.wholesale.store', $this->warehouse), $this->payload(['quantities' => [$this->stock->id => 3]]))->assertSessionHasErrors();
        $this->assertSame(2, $this->stock->fresh()->quantity);
        $this->assertSame(1, WholesaleOrder::count());
    }

    public function test_cancellation_restores_stock_once_and_blocks_further_transitions(): void
    {
        $order = $this->place();
        $this->put(route('admin.wholesale.orders.status', $order), ['status' => 'cancelled'])->assertSessionHasNoErrors();
        $this->put(route('admin.wholesale.orders.status', $order), ['status' => 'cancelled'])->assertSessionHasNoErrors();
        $this->assertSame(10, $this->stock->fresh()->quantity);
        $this->actingAs($this->manager)->put(route('admin.wholesale.orders.status', $order), ['status' => 'accepted'])->assertSessionHasErrors('status');
    }

    public function test_order_moves_through_warehouse_workflow_and_pharmacy_cannot_accept_it(): void
    {
        $order = $this->place();
        $this->put(route('admin.wholesale.orders.status', $order), ['status' => 'accepted'])->assertSessionHasErrors('status');
        $this->actingAs($this->manager)->put(route('admin.wholesale.orders.status', $order), ['status' => 'completed'])->assertSessionHasErrors('status');
        foreach (['accepted', 'preparing', 'completed'] as $status) $this->put(route('admin.wholesale.orders.status', $order), ['status' => $status])->assertSessionHasNoErrors();
        $this->assertSame('completed', $order->fresh()->status);
        $this->put(route('admin.wholesale.orders.status', $order), ['status' => 'cancelled'])->assertSessionHasErrors('status');
        $this->assertSame(7, $this->stock->fresh()->quantity);
        $this->assertDatabaseHas('admin_notifications', ['user_id' => $this->pharmacist->id, 'type' => 'wholesale']);
    }

    public function test_orders_are_private_to_warehouse_and_pharmacy(): void
    {
        $order = $this->place();
        $this->actingAs($this->otherManager)->get(route('admin.wholesale.orders'))->assertOk()->assertDontSee($order->number);
        $this->get(route('admin.wholesale.orders.show', $order))->assertForbidden();
        $this->put(route('admin.wholesale.orders.status', $order), ['status' => 'cancelled'])->assertForbidden();
        $otherPharmacy = Pharmacy::create(['name' => '{"en":"Other buyer","ar":"أخرى"}']);
        $otherPharmacist = $this->user(Role::PHARMACIST, ['pharmacy_id' => $otherPharmacy->id]);
        $this->actingAs($otherPharmacist)->get(route('admin.wholesale.orders.show', $order))->assertForbidden();
        $this->get(route('admin.wholesale.orders'))->assertOk()->assertDontSee($order->number);
    }

    public function test_disabled_warehouse_or_stock_and_expired_pharmacy_cannot_order(): void
    {
        $this->stock->update(['is_available' => false]);
        $this->actingAs($this->pharmacist)->post(route('admin.wholesale.store', $this->warehouse), $this->payload())->assertSessionHasErrors();
        $this->stock->update(['is_available' => true]);
        $this->warehouse->update(['is_active' => false]);
        $this->post(route('admin.wholesale.store', $this->warehouse), $this->payload())->assertForbidden();
        $this->warehouse->update(['is_active' => true]);
        $this->pharmacist->pharmacy->update(['subscription_ends_at' => today()->subDay()]);
        $this->post(route('admin.wholesale.store', $this->warehouse), $this->payload())->assertForbidden();
        $this->assertSame(0, WholesaleOrder::count());
    }
}
