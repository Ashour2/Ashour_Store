<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolesPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Tests\TestCase;

class AdminOrderTest extends TestCase
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

    protected function order(array $attributes = []): Order
    {
        $product = Product::create([
            'name'        => json_encode(['en' => 'Vitamin C', 'ar' => 'فيتامين سي'], JSON_UNESCAPED_UNICODE),
            'description' => json_encode(['en' => 'Supplement', 'ar' => 'مكمل'], JSON_UNESCAPED_UNICODE),
            'price'       => 20,
            'quantity'    => 10,
        ]);

        $order = Order::create($attributes + [
            'order_number'   => 'ORD-20260914-1111',
            'customer_name'  => 'Sara',
            'customer_phone' => '+966500000001',
            'total'          => 40,
            'status'         => 'new',
        ]);

        OrderDetail::create([
            'order_id' => $order->id, 'product_id' => $product->id,
            'product_name' => $product->name, 'price' => 20, 'quantity' => 2, 'total' => 40,
        ]);

        return $order;
    }

    public function test_the_orders_inbox_lists_orders(): void
    {
        $order = $this->order();

        $this->actingAs($this->admin())
            ->get(route('admin.orders.index'))
            ->assertOk()
            ->assertSee($order->order_number)
            ->assertSee('Sara');
    }

    public function test_orders_can_be_filtered_by_status(): void
    {
        $this->order();

        $this->actingAs($this->admin())
            ->get(route('admin.orders.index', ['status' => 'delivered']))
            ->assertOk()
            ->assertDontSee('ORD-20260914-1111');
    }

    public function test_an_order_shows_its_lines_and_whatsapp_handoff(): void
    {
        $order = $this->order();

        $this->actingAs($this->admin())
            ->get(route('admin.orders.show', $order->id))
            ->assertOk()
            ->assertSee('Vitamin C')
            ->assertSee('wa.me', false);
    }

    public function test_the_status_can_be_updated(): void
    {
        $order = $this->order();

        $this->actingAs($this->admin())
            ->put(route('admin.orders.update', $order->id), ['status' => 'delivered'])
            ->assertRedirect(route('admin.orders.show', $order->id));

        $this->assertSame('delivered', $order->fresh()->status);
    }

    public function test_an_order_can_be_deleted(): void
    {
        $order = $this->order();

        $this->actingAs($this->admin())
            ->delete(route('admin.orders.destroy', $order->id))
            ->assertRedirect(route('admin.orders.index'));

        $this->assertSame(0, Order::count());
    }

    public function test_deleting_a_medicine_keeps_the_order_history(): void
    {
        $order = $this->order();
        $detail = $order->order_details->first();

        Product::find($detail->product_id)->delete();

        $detail->refresh();
        $this->assertNull($detail->product_id);
        $this->assertSame('Vitamin C', $detail->trans_product_name);
        $this->assertEquals(40, $detail->total);

        $this->actingAs($this->admin())
            ->get(route('admin.orders.show', $order->id))
            ->assertOk()
            ->assertSee('Vitamin C');
    }

    public function test_guests_cannot_reach_the_orders_inbox(): void
    {
        $this->get(route('admin.orders.index'))->assertRedirect();
    }
}
