<?php

namespace Tests\Feature;

use App\Models\Cafe;
use App\Models\Customer;
use App\Models\CustomerOtp;
use App\Models\Product;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerAndUserRegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected Cafe $cafe;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cafe = Cafe::create([
            'name' => 'بی کافه شیراز',
            'slug' => 'b-cafe-shiraz',
            'status' => 'active',
        ]);

        $this->admin = User::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'مدیر تست',
            'phone' => '09170000000',
            'email' => 'admin@test.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);
    }

    public function test_customer_can_register_and_authenticate_via_otp(): void
    {
        // 1. Send OTP to new customer
        $sendResponse = $this->post('/customer/send-otp', [
            'phone' => '09171239999',
        ]);
        $sendResponse->assertSessionHas('otp_sent', true);

        // Verify OTP was stored in DB
        $otp = CustomerOtp::where('phone', '09171239999')->latest()->first();
        $this->assertNotNull($otp);
        $this->assertEquals(6, strlen($otp->code));

        // 2. Verify OTP and login
        $verifyResponse = $this->post('/customer/verify-otp', [
            'phone' => '09171239999',
            'code' => $otp->code,
        ]);
        $verifyResponse->assertRedirect(route('customer.dashboard'));

        // Customer created and logged in
        $customer = Customer::where('phone', '09171239999')->first();
        $this->assertNotNull($customer);
        $this->assertAuthenticatedAs($customer, 'customer');
    }

    public function test_unified_users_hub_renders_staff_and_customers(): void
    {
        // Add a customer
        Customer::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'سارا نادری',
            'phone' => '09172223344',
        ]);

        // Staff Tab
        $resStaff = $this->actingAs($this->admin)->get(route('admin.users.index', ['tab' => 'staff']));
        $resStaff->assertOk();
        $resStaff->assertSee('مدیر تست');

        // Customers Tab
        $resCustomers = $this->actingAs($this->admin)->get(route('admin.users.index', ['tab' => 'customers']));
        $resCustomers->assertOk();
        $resCustomers->assertSee('سارا نادری');
    }

    public function test_pos_can_redeem_subscription_with_zero_paid_items(): void
    {
        $customer = Customer::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'علی تستی',
            'phone' => '09171112233',
        ]);

        $product = Product::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'قهوه فرانسه',
            'price' => 50000,
            'status' => 'active',
        ]);

        $plan = SubscriptionPlan::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'پلن ویژه ماهانه',
            'duration_days' => 30,
            'total_items' => 30,
            'price' => 900000,
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

        // Submit via POS daily-usage.store-paid with use_free_item = 1 and empty items array
        $response = $this->actingAs($this->admin)->post(route('daily-usage.store-paid'), [
            'customer_id' => $customer->id,
            'phone' => $customer->phone,
            'use_free_item' => 1,
            'free_product_id' => $product->id,
            'subscription_id' => $subscription->id,
            'items' => [],
        ]);

        $response->assertSessionHas('success');
        $subscription->refresh();
        $this->assertEquals(29, $subscription->remaining_items);
    }
}
