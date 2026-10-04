<?php

namespace Tests\Feature;

use App\Models\Cafe;
use App\Models\Category;
use App\Models\Customer;
use App\Models\CustomerTier;
use App\Models\DeliveryZone;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\Sms\SmsGatewayInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class Phase2OnlineOrderingTest extends TestCase
{
    use RefreshDatabase;

    protected Cafe $cafe;
    protected Product $product;
    protected DeliveryZone $zone;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cafe = Cafe::create([
            'name' => 'بی کافه شیراز',
            'phone' => '07133333333',
        ]);

        $category = Category::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'قهوه بار',
            'status' => 'active',
        ]);

        $this->product = Product::create([
            'cafe_id' => $this->cafe->id,
            'category_id' => $category->id,
            'name' => 'اسپرسو دابل شیراز',
            'type' => 'coffee',
            'price' => 75000,
            'status' => 'active',
        ]);

        $this->zone = DeliveryZone::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'منطقه ۱ - قصردشت',
            'delivery_price' => 25000,
            'min_order_free_delivery' => 350000,
            'center_lat' => 29.6385,
            'center_lng' => 52.5050,
            'radius_km' => 10,
            'status' => 'active',
        ]);

        $this->admin = User::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'مدیر کافه',
            'phone' => '09121111111',
            'email' => 'admin@karasu.test',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
    }

    public function test_can_view_storefront_menu(): void
    {
        $response = $this->get('/menu');
        $response->assertStatus(200);
        $response->assertSee('اسپرسو دابل شیراز');
        $response->assertSee('قهوه بار');
    }

    public function test_can_place_dine_in_order(): void
    {
        $payload = [
            'customer_name' => 'سهراب سپهری',
            'customer_phone' => '09171234567',
            'order_type' => 'dine_in',
            'table_number' => 'میز ۴',
            'payment_method' => 'card_to_card',
            'tracking_number' => 'TRK-998877',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 2,
                ],
            ],
        ];

        $response = $this->post('/cart/order', $payload);

        $this->assertDatabaseHas('orders', [
            'order_type' => 'dine_in',
            'table_number' => 'میز ۴',
            'payment_method' => 'card_to_card',
            'total_price' => 150000,
            'delivery_fee' => 0,
            'final_payable' => 150000,
        ]);

        $order = Order::where('table_number', 'میز ۴')->first();
        $this->assertNotNull($order);
        $this->assertNotEmpty($order->order_number);

        $response->assertRedirect(route('storefront.order-status', ['order_number' => $order->order_number]));
    }

    public function test_can_place_courier_delivery_order_in_shiraz(): void
    {
        $payload = [
            'customer_name' => 'حافظ شیرازی',
            'customer_phone' => '09172223344',
            'order_type' => 'courier',
            'delivery_zone_id' => $this->zone->id,
            'address_line' => 'شیراز، خیابان قصردشت، کوچه ۱۵',
            'latitude' => 29.6385,
            'longitude' => 52.5050,
            'payment_method' => 'card_to_card',
            'tracking_number' => 'TRK-112233',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 1,
                ],
            ],
        ];

        $response = $this->post('/cart/order', $payload);

        $this->assertDatabaseHas('orders', [
            'order_type' => 'courier',
            'payment_method' => 'card_to_card',
            'total_price' => 75000,
            'delivery_fee' => 25000,
            'final_payable' => 100000,
        ]);

        $this->assertDatabaseHas('customer_addresses', [
            'address_line' => 'شیراز، خیابان قصردشت، کوچه ۱۵',
            'delivery_zone_id' => $this->zone->id,
        ]);
    }

    public function test_live_kitchen_board_feed_and_status_update(): void
    {
        $order = Order::create([
            'cafe_id' => $this->cafe->id,
            'order_number' => 'BC-TEST1',
            'order_type' => 'dine_in',
            'status' => 'pending',
            'total_price' => 75000,
            'final_payable' => 75000,
        ]);

        // Check feed as admin
        $feedResponse = $this->actingAs($this->admin)->getJson('/admin/live-orders/feed');
        $feedResponse->assertStatus(200);
        $feedResponse->assertJsonFragment(['order_number' => 'BC-TEST1']);

        // Update status to preparing
        $statusResponse = $this->actingAs($this->admin)->postJson("/admin/live-orders/{$order->id}/status", [
            'action' => 'start_prep',
        ]);
        $statusResponse->assertStatus(200);
        $this->assertEquals('preparing', $order->fresh()->status);
    }

    public function test_customer_birthdate_locking(): void
    {
        $customer = Customer::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'تست مشتری',
            'phone' => '09170001122',
        ]);

        // 1. Initial birthdate set
        $this->actingAs($customer, 'customer')->post('/panel/profile', [
            'name' => 'تست مشتری ویرایش شده',
            'birthdate' => '1995-05-15',
        ]);

        $customer->refresh();
        $this->assertEquals('1995-05-15', $customer->birthdate->format('Y-m-d'));

        // 2. Attempt to alter birthdate should be ignored
        $this->actingAs($customer, 'customer')->post('/panel/profile', [
            'name' => 'تست مشتری ویرایش مجدد',
            'birthdate' => '2000-01-01',
        ]);

        $customer->refresh();
        $this->assertEquals('1995-05-15', $customer->birthdate->format('Y-m-d'));
        $this->assertEquals('تست مشتری ویرایش مجدد', $customer->name);
    }

    public function test_birthday_rewards_command(): void
    {
        // Mock SmsGateway
        $mockSms = $this->createMock(SmsGatewayInterface::class);
        $mockSms->expects($this->once())->method('sendNotification')->willReturn(true);
        $this->app->instance(SmsGatewayInterface::class, $mockSms);

        $today = Carbon::today();

        $birthdayCustomer = Customer::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'متولد امروز',
            'phone' => '09179998877',
            'birthdate' => $today->copy()->subYears(25), // Born 25 years ago on this exact month & day
        ]);

        $this->artisan('loyalty:birthday-rewards')->assertSuccessful();

        $jalaliYear = \Morilog\Jalali\Jalalian::now()->getYear();
        $this->assertDatabaseHas('birthday_reward_logs', [
            'customer_id' => $birthdayCustomer->id,
            'year' => $jalaliYear,
        ]);

        $this->assertDatabaseHas('coupons', [
            'discount_value' => 50,
            'discount_type' => 'percent',
        ]);
    }
}
