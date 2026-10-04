<?php

namespace Tests\Feature;

use App\Models\Cafe;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\CustomerTier;
use App\Models\DeliveryZone;
use App\Models\Product;
use App\Models\SubscriptionPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerPortalAndAddressReuseTest extends TestCase
{
    use RefreshDatabase;

    protected Cafe $cafe;
    protected Customer $customer;
    protected DeliveryZone $zone;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cafe = Cafe::firstOrCreate(
            ['id' => 1],
            ['name' => 'بی کافه (B Cafe)', 'phone' => '07136280000', 'address' => 'شیراز، میدان ارم']
        );

        $this->customer = Customer::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'علی تستی',
            'phone' => '09171234567',
            'wallet_balance' => 50000,
            'total_spent' => 150000,
        ]);

        $this->zone = DeliveryZone::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'منطقه ۱ شیراز',
            'delivery_price' => 35000,
            'polygon_coordinates' => [
                ['lat' => 29.60, 'lng' => 52.50],
                ['lat' => 29.65, 'lng' => 52.50],
                ['lat' => 29.65, 'lng' => 52.55],
                ['lat' => 29.60, 'lng' => 52.55],
            ],
            'status' => 'active',
        ]);
    }

    public function test_customer_portal_pages_render_successfully(): void
    {
        $response = $this->actingAs($this->customer, 'customer')->get(route('customer.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('بی کافه (B Cafe)');
        $response->assertSee('داشبورد');

        $response = $this->actingAs($this->customer, 'customer')->get(route('customer.subscriptions'));
        $response->assertStatus(200);
        $response->assertSee('اشتراک‌های روزانه قهوه تخصصی');

        $response = $this->actingAs($this->customer, 'customer')->get(route('customer.club'));
        $response->assertStatus(200);
        $response->assertSee('باشگاه مشتریان و کش‌بک');

        $response = $this->actingAs($this->customer, 'customer')->get(route('customer.orders'));
        $response->assertStatus(200);
        $response->assertSee('تاریخچه سفارش‌های من');

        $response = $this->actingAs($this->customer, 'customer')->get(route('customer.addresses'));
        $response->assertStatus(200);
        $response->assertSee('آدرس‌های من');

        $response = $this->actingAs($this->customer, 'customer')->get(route('customer.profile'));
        $response->assertStatus(200);
        $response->assertSee('مشخصات من و هدیه تولد');
    }

    public function test_checkout_reuses_selected_saved_address_without_creating_duplicates(): void
    {
        $category = \App\Models\Category::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'نوشیدنی گرم',
            'status' => 'active',
        ]);

        $product = Product::create([
            'cafe_id' => $this->cafe->id,
            'category_id' => $category->id,
            'name' => 'آمریکانو اسپشالتی',
            'type' => 'coffee',
            'price' => 75000,
            'status' => 'active',
        ]);

        $savedAddress = CustomerAddress::create([
            'customer_id' => $this->customer->id,
            'title' => 'منزل قصردشت',
            'address_line' => 'شیراز، قصردشت، کوچه ۲۰',
            'latitude' => 29.6293,
            'longitude' => 52.5186,
            'delivery_zone_id' => $this->zone->id,
            'building_unit' => 'واحد ۲',
            'is_default' => true,
        ]);

        $initialAddressCount = CustomerAddress::where('customer_id', $this->customer->id)->count();
        $this->assertEquals(1, $initialAddressCount);

        // Submit checkout passing address_id of saved address
        $response = $this->actingAs($this->customer, 'customer')->post(route('cart.order'), [
            'customer_name' => $this->customer->name,
            'customer_phone' => $this->customer->phone,
            'order_type' => 'courier',
            'address_id' => $savedAddress->id,
            'address_line' => $savedAddress->address_line,
            'latitude' => 29.6293,
            'longitude' => 52.5186,
            'payment_method' => 'cash',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                ]
            ],
        ]);

        $response->assertRedirect();
        $finalAddressCount = CustomerAddress::where('customer_id', $this->customer->id)->count();
        $this->assertEquals(1, $finalAddressCount, 'Selecting an existing address must not create duplicate CustomerAddress records');
    }

    public function test_checkout_deduplicates_address_when_identical_coordinates_passed_without_id(): void
    {
        $category = \App\Models\Category::firstOrCreate([
            'cafe_id' => $this->cafe->id,
            'name' => 'نوشیدنی گرم',
        ], ['status' => 'active']);

        $product = Product::create([
            'cafe_id' => $this->cafe->id,
            'category_id' => $category->id,
            'name' => 'کاپوچینو کلاسیک',
            'type' => 'coffee',
            'price' => 75000,
            'status' => 'active',
        ]);

        $savedAddress = CustomerAddress::create([
            'customer_id' => $this->customer->id,
            'title' => 'محل کار ارم',
            'address_line' => 'شیراز، میدان ارم',
            'latitude' => 29.6385,
            'longitude' => 52.5186,
            'delivery_zone_id' => $this->zone->id,
        ]);

        // Submit checkout without address_id, but with same coordinates and address line
        $response = $this->actingAs($this->customer, 'customer')->post(route('cart.order'), [
            'customer_name' => $this->customer->name,
            'customer_phone' => $this->customer->phone,
            'order_type' => 'courier',
            'address_line' => 'شیراز، میدان ارم',
            'latitude' => 29.6385,
            'longitude' => 52.5186,
            'payment_method' => 'cash',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                ]
            ],
        ]);

        $response->assertRedirect();
        $finalCount = CustomerAddress::where('customer_id', $this->customer->id)->count();
        $this->assertEquals(1, $finalCount, 'Identical address line or coordinates must be deduplicated automatically');
    }
}
