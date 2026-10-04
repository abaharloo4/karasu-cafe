<?php

namespace Tests\Feature;

use App\Models\Cafe;
use App\Models\Customer;
use App\Models\CustomerTier;
use App\Models\Product;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRoutesAndEnhancementsTest extends TestCase
{
    use RefreshDatabase;

    protected Cafe $cafe;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cafe = Cafe::create([
            'name' => 'بی کافه (B Cafe) - شیراز',
            'phone' => '09171234567',
            'address' => 'شیراز، بلوار ارم، پلاک ۱۰ - بی کافه',
            'city' => 'شیراز',
        ]);

        $this->admin = User::factory()->create([
            'cafe_id' => $this->cafe->id,
            'role' => 'admin',
        ]);
    }

    public function test_admin_loyalty_dashboard_renders_successfully(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/loyalty');
        $response->assertStatus(200);
        $response->assertSee('باشگاه مشتریان');
    }

    public function test_admin_daily_usage_renders_without_null_pointer(): void
    {
        $customer = Customer::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'علی تستی',
            'phone' => '09374562316',
        ]);

        // Subscription without plan or with empty products
        $plan = SubscriptionPlan::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'پلن آزمایشی بدون محصول مستقیم',
            'duration_days' => 30,
            'total_items' => 15,
            'price' => 500000,
            'status' => 'active',
        ]);

        Subscription::create([
            'cafe_id' => $this->cafe->id,
            'customer_id' => $customer->id,
            'subscription_plan_id' => $plan->id,
            'total_items' => 15,
            'remaining_items' => 15,
            'start_date' => now()->toDateString(),
            'expire_date' => now()->addDays(30)->toDateString(),
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/daily-usage?phone=09374562316');
        $response->assertStatus(200);
        $response->assertSee('علی تستی');
    }

    public function test_legacy_routes_accessible_alongside_admin_prefix(): void
    {
        $response = $this->actingAs($this->admin)->get('/daily-usage');
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin)->get('/admin/daily-usage');
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin)->get('/orders');
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin)->get('/admin/orders');
        $response->assertStatus(200);
    }

    public function test_customer_creation_persists_birthdate(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/customers', [
            'name' => 'سهراب سپهری',
            'phone' => '09129998877',
            'birthdate' => '1990-05-15',
        ]);

        $customer = Customer::where('phone', '09129998877')->first();
        $this->assertNotNull($customer);
        $response->assertRedirect(route('customers.show', $customer));
        $this->assertEquals('1990-05-15', $customer->birthdate->format('Y-m-d'));
    }

    public function test_sms_settings_can_update_order_lifecycle_templates(): void
    {
        $response = $this->actingAs($this->admin)->put('/admin/settings/sms', [
            'username' => 'testuser',
            'from_number' => '50004000',
            'reminder_hour' => 14,
            'is_order_sms_enabled' => 1,
            'template_text_order_submitted' => 'سلام {name} سفارش {order_number} با موفقیت ثبت گردید.',
            'pattern_order_submitted' => '99001',
            'template_text_order_ready' => 'سلام {name} سفارش {order_number} آماده است.',
            'pattern_order_ready' => '99002',
        ]);

        $response->assertRedirect();
        $setting = \App\Models\SmsSetting::first();
        $this->assertTrue((bool)$setting->is_order_sms_enabled);
        $this->assertEquals('99001', $setting->pattern_order_submitted);
    }

    public function test_manual_order_can_redeem_active_subscription_credit(): void
    {
        $customer = Customer::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'رضا سعادت',
            'phone' => '09173334455',
        ]);

        $product = Product::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'اسپرسو دوبل',
            'price' => 60000,
            'type' => 'physical',
            'status' => 'active',
        ]);

        $plan = SubscriptionPlan::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'اشتراک طلایی ۳۰ فنجان',
            'duration_days' => 30,
            'total_items' => 30,
            'price' => 1200000,
            'status' => 'active',
        ]);
        $plan->products()->attach($product->id);

        $subscription = Subscription::create([
            'cafe_id' => $this->cafe->id,
            'customer_id' => $customer->id,
            'subscription_plan_id' => $plan->id,
            'total_items' => 30,
            'remaining_items' => 30,
            'start_date' => now()->toDateString(),
            'expire_date' => now()->addDays(30)->toDateString(),
            'status' => 'active',
        ]);

        // Place manual order with redeem_subscription = 1
        $response = $this->actingAs($this->admin)->post('/admin/orders', [
            'customer_id' => $customer->id,
            'redeem_subscription' => 1,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                ]
            ],
        ]);

        $order = \App\Models\Order::latest()->first();
        $response->assertRedirect(route('orders.show', $order));

        // Verify remaining items decreased by 1
        $subscription->refresh();
        $this->assertEquals(29, $subscription->remaining_items);

        // Verify subscription usage record created
        $this->assertDatabaseHas('subscription_usages', [
            'subscription_id' => $subscription->id,
            'product_id' => $product->id,
            'customer_id' => $customer->id,
        ]);
    }
}
