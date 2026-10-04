<?php

namespace Tests\Feature;

use App\Models\Cafe;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\DeliveryZone;
use App\Models\LoyaltySetting;
use App\Models\Order;
use App\Models\PaymentGateway;
use App\Models\ReceiptSetting;
use App\Models\SmsSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsAndLiveOrdersTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Cafe $cafe;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cafe = Cafe::create([
            'name' => 'بی کافه (شیراز)',
            'phone' => '07136000000',
            'address' => 'شیراز، خیابان ارم',
        ]);

        $this->admin = User::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'مدیر کافه',
            'email' => 'admin@bcafe.ir',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);
    }

    public function test_admin_can_save_receipt_settings(): void
    {
        $payload = [
            'paper_width' => '58mm',
            'font_family' => 'iransans',
            'font_size' => 'large',
            'header_title' => 'کافه رستوران بی شیراز',
            'header_subtitle' => 'خیابان ارم • تلفن ۳۶۰۰۰۰۰۰',
            'invoice_title' => 'صورت‌حساب قطعی فروش',
            'show_logo' => '1',
            'show_order_number' => '1',
            'show_date_time' => '1',
            'show_cashier_name' => '1',
            'show_customer_name' => '1',
            'show_customer_phone' => '1',
            'show_customer_club_info' => '1',
            'show_order_type' => '1',
            'show_delivery_address' => '1',
            'show_item_row_number' => '1',
            'show_item_unit_price' => '1',
            'show_item_notes' => '1',
            'show_subtotal' => '1',
            'show_discount' => '1',
            'show_delivery_fee' => '1',
            'show_tax' => '1',
            'tax_percent' => '9.5',
            'show_amount_in_words' => '1',
            'show_payment_method' => '1',
            'show_payment_status' => '1',
            'show_barcode' => '1',
            'show_qr_code' => '1',
            'qr_code_type' => 'custom',
            'custom_qr_url' => 'https://bcafe.ir/track',
            'footer_message' => 'سپاس از حسن انتخاب شما - بی کافه',
            'show_wifi' => '1',
            'wifi_ssid' => 'BCafe-Vip-Wifi',
            'wifi_password' => 'Pass2026',
            'show_instagram' => '1',
            'instagram_handle' => '@bcafe.shiraz',
            'auto_print' => '1',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('settings.receipt.update'), $payload);

        $response->assertRedirect(route('settings.receipt.edit'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('receipt_settings', [
            'cafe_id' => $this->cafe->id,
            'paper_width' => '58mm',
            'font_family' => 'iransans',
            'font_size' => 'large',
            'header_title' => 'کافه رستوران بی شیراز',
            'invoice_title' => 'صورت‌حساب قطعی فروش',
            'custom_qr_url' => 'https://bcafe.ir/track',
            'wifi_ssid' => 'BCafe-Vip-Wifi',
            'auto_print' => 1,
            'show_delivery_address' => 1,
        ]);
    }

    public function test_admin_can_update_sms_settings(): void
    {
        $response = $this->actingAs($this->admin)
            ->put(route('settings.sms.update'), [
                'username' => '09121234567',
                'password' => 'newSecretPass',
                'from_number' => '50004000',
                'reminder_hour' => 19,
                'is_reminder_enabled' => '1',
                'is_welcome_enabled' => '1',
                'is_daily_usage_enabled' => '1',
                'template_text_welcome' => 'سلام {name} عزیز، اشتراک {plan_name} فعال شد.',
            ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('sms_settings', [
            'cafe_id' => $this->cafe->id,
            'username' => '09121234567',
            'reminder_hour' => 19,
            'is_reminder_enabled' => 1,
        ]);
    }

    public function test_admin_can_update_payment_settings(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('settings.payment.update'), [
                'zarinpal_is_active' => '1',
                'zarinpal_merchant_id' => '11111111-2222-3333-4444-555555555555',
                'zarinpal_currency' => 'IRT',
                'card_to_card_is_active' => '1',
                'card_bank_name' => 'بانک ملی ایران',
                'card_number' => '6037991122334455',
                'card_sheba_number' => 'IR120170000000112233445566',
                'card_account_owner' => 'امیر محمد بهارلو',
                'cash_is_active' => '1',
            ]);

        $response->assertRedirect(route('settings.payment.edit'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('payment_gateways', [
            'key' => 'zarinpal',
            'is_active' => 1,
        ]);

        $this->assertDatabaseHas('payment_gateways', [
            'key' => 'card_to_card',
            'is_active' => 1,
        ]);
    }

    public function test_admin_can_update_loyalty_settings(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('loyalty.settings.update'), [
                'is_active' => '1',
                'cashback_percent' => 7.5,
                'min_order_amount' => 50000,
                'points_per_order_unit' => 15000,
                'welcome_bonus_points' => 30,
                'birthday_reward_amount' => 70000,
            ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('loyalty_settings', [
            'cafe_id' => $this->cafe->id,
            'cashback_percent' => 7.5,
            'min_order_amount' => 50000,
            'points_per_order_unit' => 15000,
            'welcome_bonus_points' => 30,
            'birthday_reward_amount' => 70000,
        ]);
    }

    public function test_live_orders_feed_returns_accurate_customer_address_and_coordinates(): void
    {
        $customer = Customer::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'رضا حسینی',
            'phone' => '09171234567',
        ]);

        $zone = DeliveryZone::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'منطقه ارم شیراز',
            'delivery_price' => 25000,
            'status' => 'active',
        ]);

        $address = CustomerAddress::create([
            'customer_id' => $customer->id,
            'title' => 'منزل ارم',
            'address_line' => 'شیراز، میدان ارم، کوچه ۴، پلاک ۱۲',
            'latitude' => 29.6293,
            'longitude' => 52.5186,
            'delivery_zone_id' => $zone->id,
            'building_unit' => 'واحد ۳',
        ]);

        $order = Order::create([
            'cafe_id' => $this->cafe->id,
            'order_number' => 'BC-LIVE-001',
            'customer_id' => $customer->id,
            'type' => 'normal',
            'order_type' => 'courier',
            'fulfillment_type' => 'delivery',
            'delivery_address_id' => $address->id,
            'payment_method' => 'card_to_card',
            'status' => 'pending',
            'total_price' => 150000,
            'final_payable' => 175000,
            'delivery_fee' => 25000,
            'notes' => 'لطفاً زنگ واحد ۳ را بزنید',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.live-orders.feed'));

        $response->assertStatus(200);
        $data = $response->json();

        $this->assertArrayHasKey('orders', $data);
        $this->assertNotEmpty($data['orders']);

        $feedOrder = collect($data['orders'])->firstWhere('id', $order->id);
        $this->assertNotNull($feedOrder);
        $this->assertEquals('شیراز، میدان ارم، کوچه ۴، پلاک ۱۲', $feedOrder['address']);
        $this->assertEquals('منطقه ارم شیراز', $feedOrder['delivery_zone']);
        $this->assertEquals('واحد ۳', $feedOrder['unit']);
        $this->assertEquals(29.6293, $feedOrder['latitude']);
        $this->assertEquals(52.5186, $feedOrder['longitude']);
        $this->assertStringContainsString('neshan.org', $feedOrder['neshan_url']);
        $this->assertStringContainsString('google.com/maps', $feedOrder['google_maps_url']);
        $this->assertStringContainsString('balad.ir', $feedOrder['balad_url']);
        $this->assertEquals('لطفاً زنگ واحد ۳ را بزنید', $feedOrder['order_notes']);
    }

    public function test_live_orders_feed_handles_customer_address_fallback_when_delivery_address_id_is_null(): void
    {
        $customer = Customer::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'سارا احمدی',
            'phone' => '09179876543',
        ]);

        CustomerAddress::create([
            'customer_id' => $customer->id,
            'title' => 'محل کار',
            'address_line' => 'شیراز، خیابان ملاصدرا، پاساژ دنا',
            'latitude' => 29.6350,
            'longitude' => 52.5100,
            'building_unit' => 'طبقه دوم',
        ]);

        $order = Order::create([
            'cafe_id' => $this->cafe->id,
            'order_number' => 'BC-FALLBACK-002',
            'customer_id' => $customer->id,
            'type' => 'normal',
            'order_type' => 'courier',
            'fulfillment_type' => 'delivery',
            'delivery_address_id' => null, // null direct id
            'payment_method' => 'zarinpal',
            'status' => 'pending',
            'total_price' => 80000,
            'final_payable' => 80000,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.live-orders.feed'));

        $response->assertStatus(200);
        $data = $response->json();

        $feedOrder = collect($data['orders'])->firstWhere('id', $order->id);
        $this->assertNotNull($feedOrder);
        $this->assertEquals('شیراز، خیابان ملاصدرا، پاساژ دنا', $feedOrder['address']);
        $this->assertEquals(29.6350, $feedOrder['latitude']);
        $this->assertEquals(52.5100, $feedOrder['longitude']);
    }
}
