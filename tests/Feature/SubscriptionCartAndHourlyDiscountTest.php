<?php

namespace Tests\Feature;

use App\Models\Cafe;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionCartAndHourlyDiscountTest extends TestCase
{
    use RefreshDatabase;

    protected Cafe $cafe;
    protected User $admin;
    protected Customer $customer;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cafe = Cafe::create([
            'name' => 'بی کافه شیراز',
            'status' => 'open',
            'opening_time' => '07:00',
            'closing_time' => '23:59',
        ]);

        $this->admin = User::create([
            'name' => 'مدیر کافه',
            'email' => 'admin@bcafe.ir',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'cafe_id' => $this->cafe->id,
        ]);

        $this->customer = Customer::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'امیر شیرازی',
            'phone' => '09171234567',
            'wallet_balance' => 1000000,
        ]);

        $this->category = Category::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'اسپرسو بار',
            'slug' => 'espresso-bar',
        ]);
    }

    public function test_hourly_discount_active_calculation(): void
    {
        $product = Product::create([
            'cafe_id' => $this->cafe->id,
            'category_id' => $this->category->id,
            'name' => 'آمریکانو هپی هاور',
            'price' => 100000,
            'status' => 'active',
            'discount_percent' => 20,
            'discount_start_time' => '00:00',
            'discount_end_time' => '23:59',
            'is_hourly_discount_active' => true,
        ]);

        $this->assertTrue($product->isHourlyDiscountActive());
        $this->assertEquals(80000, $product->effective_price);
    }

    public function test_hourly_discount_inactive_when_flag_false(): void
    {
        $product = Product::create([
            'cafe_id' => $this->cafe->id,
            'category_id' => $this->category->id,
            'name' => 'کاپوچینو معمولی',
            'price' => 100000,
            'status' => 'active',
            'discount_percent' => 20,
            'discount_start_time' => '00:00',
            'discount_end_time' => '23:59',
            'is_hourly_discount_active' => false,
        ]);

        $this->assertFalse($product->isHourlyDiscountActive());
        $this->assertEquals(100000, $product->effective_price);
    }

    public function test_placing_order_with_subscription_plan_in_cart_activates_subscription_via_wallet(): void
    {
        $plan = SubscriptionPlan::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'اشتراک طلایی ۳۰ روزه',
            'price' => 300000,
            'total_items' => 20,
            'duration_days' => 30,
            'status' => 'active',
        ]);

        $product = Product::create([
            'cafe_id' => $this->cafe->id,
            'category_id' => $this->category->id,
            'name' => 'لاته ارگانیک',
            'price' => 80000,
            'status' => 'active',
        ]);

        $this->actingAs($this->customer, 'customer');

        $response = $this->post(route('cart.order'), [
            'customer_name' => $this->customer->name,
            'customer_phone' => $this->customer->phone,
            'order_type' => 'dine_in',
            'payment_method' => 'cash',
            'use_wallet' => 1,
            'items' => [
                [
                    'type' => 'subscription',
                    'subscription_plan_id' => $plan->id,
                    'quantity' => 1,
                ],
                [
                    'type' => 'product',
                    'product_id' => $product->id,
                    'quantity' => 1,
                ],
            ],
        ]);

        $response->assertRedirect();

        // Customer wallet should be charged (300,000 plan + 80,000 product = 380,000)
        $this->customer->refresh();
        $this->assertEquals(620000, (float) $this->customer->wallet_balance);

        // Subscription must be activated for the customer
        $this->assertNotNull($this->customer->activeSubscription);
        $this->assertEquals($plan->id, $this->customer->activeSubscription->subscription_plan_id);
        $this->assertEquals(20, $this->customer->activeSubscription->remaining_items);

        // Order item should record subscription_plan_id
        $order = Order::latest()->first();
        $this->assertEquals('preparing', $order->status);
        $this->assertCount(2, $order->items);
        $subItem = $order->items->firstWhere('item_type', 'subscription');
        $this->assertNotNull($subItem);
        $this->assertEquals($plan->id, $subItem->subscription_plan_id);
    }

    public function test_subscription_purchase_route_redirects_to_cart_with_add_plan_param(): void
    {
        $plan = SubscriptionPlan::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'اشتراک نقره‌ای',
            'price' => 150000,
            'total_items' => 10,
            'duration_days' => 15,
            'status' => 'active',
        ]);

        $response = $this->get(route('subscription.purchase', $plan));

        $response->assertRedirect(route('cart.index', ['add_plan' => $plan->id]));
        $response->assertSessionHas('success');
    }
}
