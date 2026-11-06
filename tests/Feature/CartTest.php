<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Tests\TestCase;
use App\Models\Pharmacy;

class CartTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Under the console kernel the site routes carry no locale prefix, so
        // the locale redirect filters would bounce every request to "/en".
        $this->withoutMiddleware([LaravelLocalizationRedirectFilter::class, LocaleSessionRedirect::class]);
    }

    protected function medicine(array $attributes = []): Product
    {
        return Product::create($attributes + [
            'name'        => json_encode(['en' => 'Paracetamol', 'ar' => 'باراسيتامول'], JSON_UNESCAPED_UNICODE),
            'description' => json_encode(['en' => 'Pain relief', 'ar' => 'مسكن'], JSON_UNESCAPED_UNICODE),
            'price'       => 12.50,
            'quantity'    => 25,
            'is_active'   => true,
        ]);
    }

    public function test_a_medicine_can_be_added_to_the_cart(): void
    {
        $medicine = $this->medicine();

        $response = $this->postJson(route('cart.add', $medicine->id) . '?quantity=3');

        $response->assertOk()
            ->assertJsonPath('count', 3)
            ->assertJsonPath('total', 37.5)
            ->assertJsonPath('items.0.name', 'Paracetamol');
    }

    public function test_quantity_is_capped_at_the_stock_on_hand(): void
    {
        $medicine = $this->medicine(['quantity' => 2]);

        $this->postJson(route('cart.add', $medicine->id) . '?quantity=9')
            ->assertJsonPath('count', 2);
    }

    public function test_an_out_of_stock_medicine_cannot_be_added(): void
    {
        $medicine = $this->medicine(['quantity' => 0]);

        $this->postJson(route('cart.add', $medicine->id))
            ->assertStatus(422)
            ->assertJsonPath('count', 0);
    }

    public function test_a_line_can_be_updated_and_removed(): void
    {
        $medicine = $this->medicine();
        $this->postJson(route('cart.add', $medicine->id) . '?quantity=4');

        $this->postJson(route('cart.update', $medicine->id) . '?quantity=2')
            ->assertJsonPath('count', 2);

        $this->postJson(route('cart.remove', $medicine->id))
            ->assertJsonPath('count', 0);
    }

    public function test_checkout_warns_about_medicines_needing_a_prescription(): void
    {
        $rx = $this->medicine(['requires_prescription' => true]);
        $this->postJson(route('cart.add', $rx->id));

        $this->get(route('cart.checkout'))
            ->assertOk()
            ->assertSee(__('site.rx_notice_title'))
            ->assertSee(__('site.rx_notice_body'))
            ->assertSee('Paracetamol');
    }

    public function test_checkout_stays_quiet_when_no_prescription_is_needed(): void
    {
        $this->postJson(route('cart.add', $this->medicine()->id));

        $this->get(route('cart.checkout'))
            ->assertOk()
            ->assertDontSee(__('site.rx_notice_title'));
    }

    public function test_checkout_stores_the_order_and_empties_the_cart(): void
    {
        $medicine = $this->medicine();
        $pharmacy = Pharmacy::create(['name' => '{"en":"Branch","ar":"Branch"}', 'is_active' => true, 'subscription_status' => 'active']);
        $pharmacy->products()->attach($medicine->id, ['quantity' => 25, 'is_allowed' => true]);
        $this->postJson(route('cart.add', $medicine->id) . '?quantity=2');

        $response = $this->post(route('cart.store'), [
            'customer_name'    => 'Ahmad',
            'customer_phone'   => '+966500000000',
            'customer_address' => 'Riyadh',
            'note'             => 'Please call before delivery',
            'pharmacy_id'      => $pharmacy->id,
        ]);

        $order = Order::first();
        $this->assertNotNull($order);
        $response->assertRedirect(route('cart.success', $order->order_number));

        $this->assertSame('Ahmad', $order->customer_name);
        $this->assertSame('new', $order->status);
        $this->assertEquals(25.0, $order->total);

        $detail = $order->order_details->first();
        $this->assertSame(2, $detail->quantity);
        $this->assertEquals(12.50, $detail->price);
        $this->assertSame('Paracetamol', $detail->trans_product_name);

        // Cart is emptied once the order is placed
        $this->getJson(route('cart.index'))->assertJsonPath('count', 0);
    }

    public function test_checkout_requires_a_name_and_phone(): void
    {
        $medicine = $this->medicine();
        $this->post(route('cart.add', $medicine->id));

        $this->post(route('cart.store'), ['customer_name' => '', 'customer_phone' => ''])
            ->assertSessionHasErrors(['customer_name', 'customer_phone']);

        $this->assertSame(0, Order::count());
    }

    public function test_checkout_is_blocked_when_the_cart_is_empty(): void
    {
        $this->get(route('cart.checkout'))->assertRedirect(route('medicines.index'));
        $this->post(route('cart.store'), [
            'customer_name'  => 'Ahmad',
            'customer_phone' => '+966500000000',
        ])->assertRedirect(route('medicines.index'));

        $this->assertSame(0, Order::count());
    }

    public function test_the_success_page_shows_the_order_and_its_whatsapp_link(): void
    {
        Setting::put('whatsapp_number', '966500000000');

        $medicine = $this->medicine();
        $pharmacy = Pharmacy::create(['name' => '{"en":"Branch","ar":"Branch"}', 'is_active' => true, 'subscription_status' => 'active', 'phone' => '966500000000']);
        $pharmacy->products()->attach($medicine->id, ['quantity' => 25, 'is_allowed' => true]);
        $this->post(route('cart.add', $medicine->id));
        $this->post(route('cart.store'), [
            'customer_name'  => 'Ahmad',
            'customer_phone' => '+966500000000',
            'pharmacy_id' => $pharmacy->id,
        ]);

        $order = Order::first();

        $this->get(route('cart.success', $order->order_number))
            ->assertOk()
            ->assertSee($order->order_number)
            ->assertSee('wa.me', false);
    }

    public function test_checkout_uses_each_medicines_own_pharmacy_price(): void
    {
        $first = $this->medicine();
        $second = $this->medicine(['price' => 40]);
        $pharmacy = Pharmacy::create(['name' => '{"en":"Branch","ar":"Branch"}', 'is_active' => true, 'subscription_status' => 'active']);
        $pharmacy->products()->attach([
            $first->id => ['quantity' => 10, 'price' => 7.5, 'is_allowed' => true],
            $second->id => ['quantity' => 10, 'price' => 23, 'is_allowed' => true],
        ]);
        $this->post(route('cart.add', $first), ['quantity' => 2]);
        $this->post(route('cart.add', $second), ['quantity' => 1]);
        $this->post(route('cart.store'), ['customer_name' => 'Ahmad', 'customer_phone' => '123456', 'pharmacy_id' => $pharmacy->id])
            ->assertSessionHasNoErrors()->assertRedirect();
        $order = Order::firstOrFail();
        $this->assertEquals(38, $order->total);
        $this->assertDatabaseHas('order_details', ['order_id' => $order->id, 'product_id' => $first->id, 'price' => 7.5, 'total' => 15]);
        $this->assertDatabaseHas('order_details', ['order_id' => $order->id, 'product_id' => $second->id, 'price' => 23, 'total' => 23]);
        $this->assertDatabaseHas('pharmacy_product', ['pharmacy_id' => $pharmacy->id, 'product_id' => $first->id, 'quantity' => 8]);
    }
}
