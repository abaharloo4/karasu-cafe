<?php

namespace Tests\Feature;

use App\Models\Cafe;
use App\Models\Category;
use App\Models\Customer;
use App\Models\CustomerOtp;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class SecurityAndAuditRemediationTest extends TestCase
{
    use RefreshDatabase;

    protected Cafe $cafe;
    protected Category $category;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cafe = Cafe::create([
            'id' => 1,
            'name' => 'بی کافه شیراز',
            'phone' => '07133333333',
        ]);

        $this->category = Category::create([
            'cafe_id' => 1,
            'name' => 'اسپرسو بار',
            'slug' => 'espresso',
        ]);

        $this->product = Product::create([
            'cafe_id' => 1,
            'category_id' => $this->category->id,
            'name' => 'کاپوچینو تخصصی',
            'price' => 85000,
            'status' => 'active',
        ]);
    }

    /**
     * Test 1: Web Customer OTP Login Flow
     */
    public function test_customer_web_otp_login_flow(): void
    {
        $phone = '09171112233';

        // 1. Send OTP
        $sendRes = $this->post('/customer/send-otp', ['phone' => $phone]);
        $sendRes->assertRedirect();
        $this->assertDatabaseHas('customer_otps', ['phone' => $phone]);

        $otp = CustomerOtp::where('phone', $phone)->latest()->first();
        $this->assertNotNull($otp);

        // 2. Verify OTP
        $verifyRes = $this->post('/customer/verify-otp', [
            'phone' => $phone,
            'code' => $otp->code,
        ]);

        $verifyRes->assertRedirect(route('customer.dashboard'));
        $this->assertAuthenticated('customer');
    }

    /**
     * Test 2: SEC-02 - Subscription credit rejection when customer has no active sub
     */
    public function test_subscription_credit_rejected_without_active_subscription(): void
    {
        $response = $this->post('/cart/order', [
            'customer_name' => 'علی تستی',
            'customer_phone' => '09175554433',
            'order_type' => 'dine_in',
            'table_number' => 'میز ۳',
            'payment_method' => 'subscription_credit',
            'items' => [
                ['product_id' => $this->product->id, 'quantity' => 1],
            ],
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('orders', [
            'table_number' => 'میز ۳',
            'payment_method' => 'subscription_credit',
        ]);
    }

    /**
     * Test 3: SEC-03 - BOLA/IDOR protection on API
     */
    public function test_api_customer_cannot_access_other_customers_or_list_all(): void
    {
        $customerA = Customer::create([
            'cafe_id' => 1,
            'name' => 'مشتری الف',
            'phone' => '09171110001',
        ]);

        $customerB = Customer::create([
            'cafe_id' => 1,
            'name' => 'مشتری ب',
            'phone' => '09171110002',
        ]);

        // Customer A tries to access index
        $resIndex = $this->actingAs($customerA, 'sanctum')->getJson('/api/customers');
        $resIndex->assertStatus(403);

        // Customer A tries to view Customer B's profile
        $resShow = $this->actingAs($customerA, 'sanctum')->getJson('/api/customers/' . $customerB->id);
        $resShow->assertStatus(403);

        // Customer A can view their own profile
        $resOwn = $this->actingAs($customerA, 'sanctum')->getJson('/api/customers/' . $customerA->id);
        $resOwn->assertStatus(200)
            ->assertJsonPath('data.name', 'مشتری الف');
    }

    /**
     * Test 4: REQ-01 - Item notes are preserved in OrderItem
     */
    public function test_order_item_barista_notes_are_saved(): void
    {
        $response = $this->post('/cart/order', [
            'customer_name' => 'سارا احمدی',
            'customer_phone' => '09179998877',
            'order_type' => 'dine_in',
            'table_number' => 'میز ۷',
            'payment_method' => 'card_to_card',
            'tracking_number' => 'TRK-987654',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 2,
                    'notes' => 'کم شکر و با فوم داغ',
                ],
            ],
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('order_items', [
            'product_id' => $this->product->id,
            'quantity' => 2,
            'notes' => 'کم شکر و با فوم داغ',
        ]);
    }

    /**
     * Test 5: SEC-04 - Idempotency on Zarinpal Callback
     */
    public function test_payment_callback_is_idempotent(): void
    {
        $customer = Customer::create([
            'cafe_id' => 1,
            'name' => 'رضا حسینی',
            'phone' => '09176665544',
            'total_spent' => 100000,
        ]);

        $order = Order::create([
            'cafe_id' => 1,
            'order_number' => 'BC-TEST1',
            'customer_id' => $customer->id,
            'type' => 'normal',
            'order_type' => 'dine_in',
            'payment_method' => 'zarinpal',
            'status' => 'processing', // Already completed / verified
            'total_price' => 50000,
            'final_payable' => 50000,
        ]);

        // Replay callback request on already-processed order
        $res = $this->get('/payment/zarinpal/callback?order_id=' . $order->id . '&Status=OK&Authority=A00001');

        $res->assertRedirect();
        $customer->refresh();

        // Customer's total_spent should remain 100000, NOT incremented to 150000
        $this->assertEquals(100000, (float) $customer->total_spent);
    }

    /**
     * Test 6: Payment Settings Page Renders Safely with Encrypted/Plaintext Gateways
     */
    public function test_payment_settings_page_renders_safely(): void
    {
        $admin = User::create([
            'cafe_id' => 1,
            'name' => 'مدیر کافه',
            'phone' => '09121111111',
            'email' => 'admin@karasu.test',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        \App\Models\PaymentGateway::create([
            'key' => 'zarinpal',
            'title' => 'زرین‌پال',
            'is_active' => true,
            'settings' => ['merchant_id' => '1234-5678', 'currency' => 'IRT', 'sandbox' => true],
        ]);

        \App\Models\PaymentGateway::create([
            'key' => 'card_to_card',
            'title' => 'کارت به کارت',
            'is_active' => true,
            'settings' => ['card_number' => '6037991122334455', 'account_owner' => 'کافه بی'],
        ]);

        $res = $this->actingAs($admin)->get('/settings/payment');
        $res->assertStatus(200);
        $res->assertSee('زرین‌پال');
        $res->assertSee('کارت به کارت');
    }

    /**
     * Test 7: Customer Panel Dashboard & customer.orders.show Route Resolution
     */
    public function test_customer_panel_dashboard_and_order_show(): void
    {
        $customer = Customer::create([
            'cafe_id' => 1,
            'name' => 'مشتری پنل',
            'phone' => '09170009988',
        ]);

        $order = Order::create([
            'cafe_id' => 1,
            'order_number' => 'BC-PANEL1',
            'customer_id' => $customer->id,
            'type' => 'normal',
            'order_type' => 'dine_in',
            'payment_method' => 'card_to_card',
            'status' => 'pending',
            'total_price' => 50000,
            'final_payable' => 50000,
        ]);

        // Dashboard should render with order factor link
        $res = $this->actingAs($customer, 'customer')->get('/panel');
        $res->assertStatus(200);
        $res->assertSee('BC-PANEL1');
        $res->assertSee(route('customer.orders.show', $order));

        // Order Show should render order details
        $showRes = $this->actingAs($customer, 'customer')->get(route('customer.orders.show', $order));
        $showRes->assertStatus(200);
        $showRes->assertSee('BC-PANEL1');
    }

    /**
     * Test 8: Storefront Checkout Renders Card-to-Card Without DecryptException
     */
    public function test_checkout_page_renders_with_card_to_card(): void
    {
        \App\Models\PaymentGateway::create([
            'key' => 'card_to_card',
            'title' => 'کارت به کارت',
            'is_active' => true,
            'settings' => ['card_number' => '6037991122334455', 'account_owner' => 'کافه بی', 'bank_name' => 'بانک سامان'],
        ]);

        $res = $this->get('/cart/checkout');
        $res->assertStatus(200);
        $res->assertSee('6037991122334455');
    }
}
