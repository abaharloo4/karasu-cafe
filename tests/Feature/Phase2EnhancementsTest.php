<?php

namespace Tests\Feature;

use App\Models\Cafe;
use App\Models\Customer;
use App\Models\LoyaltySetting;
use App\Models\Order;
use App\Models\PaymentGateway;
use App\Models\Product;
use App\Models\User;
use App\Services\CashbackService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase2EnhancementsTest extends TestCase
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

    public function test_admin_can_export_orders_csv_with_utf8_bom(): void
    {
        $order = Order::create([
            'cafe_id' => $this->cafe->id,
            'order_number' => 'BC-TEST-001',
            'type' => 'normal',
            'order_type' => 'dine_in',
            'status' => 'completed',
            'total_price' => 120000,
            'final_payable' => 120000,
        ]);

        $response = $this->actingAs($this->admin)->get(route('reports.export.orders'));

        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->headers->get('content-type'), 'text/csv'));
        $content = $response->streamedContent();

        // Must start with UTF-8 BOM \xEF\xBB\xBF
        $this->assertStringStartsWith("\xEF\xBB\xBF", $content);
        $this->assertStringContainsString('BC-TEST-001', $content);
        $this->assertStringContainsString('120000', $content);
    }

    public function test_admin_can_export_products_sales_csv(): void
    {
        Product::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'اسپرسو دوبل تست',
            'type' => 'coffee',
            'price' => 65000,
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->admin)->get(route('reports.export.products'));

        $response->assertStatus(200);
        $content = $response->streamedContent();
        $this->assertStringStartsWith("\xEF\xBB\xBF", $content);
        $this->assertStringContainsString('اسپرسو دوبل تست', $content);
        $this->assertStringContainsString('65000', $content);
    }

    public function test_admin_can_export_customers_csv(): void
    {
        Customer::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'امیر رضایی',
            'phone' => '09171112233',
            'wallet_balance' => 50000,
        ]);

        $response = $this->actingAs($this->admin)->get(route('reports.export.customers'));

        $response->assertStatus(200);
        $content = $response->streamedContent();
        $this->assertStringStartsWith("\xEF\xBB\xBF", $content);
        $this->assertStringContainsString('امیر رضایی', $content);
        $this->assertStringContainsString('09171112233', $content);
    }

    public function test_admin_can_update_cashback_and_loyalty_settings(): void
    {
        $response = $this->actingAs($this->admin)->post(route('loyalty.settings.update'), [
            'is_active' => '1',
            'cashback_percent' => '7.5',
            'min_order_amount' => '80000',
            'points_per_order_unit' => '15000',
            'welcome_bonus_points' => '30',
            'birthday_reward_amount' => '70000',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $setting = LoyaltySetting::getForCafe($this->cafe->id);
        $this->assertEquals(7.5, (float) $setting->cashback_percent);
        $this->assertEquals(80000, (float) $setting->min_order_amount);
        $this->assertEquals(15000, $setting->points_per_order_unit);
    }

    public function test_cashback_service_applies_dynamic_settings(): void
    {
        $setting = LoyaltySetting::getForCafe($this->cafe->id);
        $setting->update([
            'is_active' => true,
            'cashback_percent' => 10.0,
            'min_order_amount' => 100000,
        ]);

        $customer = Customer::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'سارا احمدی',
            'phone' => '09172223344',
            'wallet_balance' => 0,
        ]);

        $cashbackService = app(CashbackService::class);

        // Order 1: Under min order amount -> 0 cashback
        $orderSmall = Order::create([
            'cafe_id' => $this->cafe->id,
            'customer_id' => $customer->id,
            'order_number' => 'ORD-SMALL',
            'type' => 'normal',
            'final_payable' => 50000,
        ]);
        $awarded = $cashbackService->awardOrderCashback($orderSmall);
        $this->assertEquals(0, $awarded);
        $this->assertEquals(0, (float) $customer->fresh()->wallet_balance);

        // Order 2: Qualifies (150,000 * 10% = 15,000)
        $orderQualifying = Order::create([
            'cafe_id' => $this->cafe->id,
            'customer_id' => $customer->id,
            'order_number' => 'ORD-BIG',
            'type' => 'normal',
            'final_payable' => 150000,
        ]);
        $awardedBig = $cashbackService->awardOrderCashback($orderQualifying);
        $this->assertEquals(15000, $awardedBig);
        $this->assertEquals(15000, (float) $customer->fresh()->wallet_balance);
    }

    public function test_payment_settings_page_and_cash_gateway_toggle(): void
    {
        $response = $this->actingAs($this->admin)->get(route('settings.payment.edit'));
        $response->assertStatus(200);
        $response->assertSee('پرداخت در محل / کارتخوان سیار و حضوری');

        // Toggle cash gateway
        $postResponse = $this->actingAs($this->admin)->post(route('settings.payment.update'), [
            'zarinpal_is_active' => '1',
            'zarinpal_merchant_id' => '12345678-1234-1234-1234-123456789012',
            'zarinpal_currency' => 'IRT',
            'card_to_card_is_active' => '1',
            'card_number' => '6037990000000000',
            'cash_is_active' => '1',
            'cash_instructions' => 'امکان پرداخت نقدی یا با کارتخوان سیار',
        ]);

        $postResponse->assertRedirect(route('settings.payment.edit'));
        $postResponse->assertSessionHas('success');

        $this->assertDatabaseHas('payment_gateways', [
            'key' => 'cash',
            'is_active' => true,
        ]);
    }

    public function test_thermal_receipt_80mm_renders_properly(): void
    {
        $order = Order::create([
            'cafe_id' => $this->cafe->id,
            'order_number' => 'BC-PRINT-01',
            'type' => 'normal',
            'order_type' => 'dine_in',
            'table_number' => '4',
            'status' => 'preparing',
            'total_price' => 185000,
            'final_payable' => 185000,
        ]);

        $response = $this->actingAs($this->admin)->get(route('orders.receipt', $order));

        $response->assertStatus(200);
        $response->assertSee('صورت‌حساب فروش');
        $response->assertSee('BC-PRINT-01');
        $response->assertSee('صد و هشتاد و پنج هزار تومان');
    }

    public function test_storefront_subscriptions_dedicated_index_page_loads(): void
    {
        $plan = \App\Models\SubscriptionPlan::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'پلن حرفه‌ای تست',
            'duration_days' => 30,
            'total_items' => 30,
            'price' => 750000,
            'status' => 'active',
        ]);

        $response = $this->get(route('storefront.subscriptions.index'));
        $response->assertStatus(200);
        $response->assertSee('باشگاه اشتراک روزانه قهوه');
        $response->assertSee('پلن حرفه‌ای تست');
        $response->assertSee('750,000 تومان');
        $response->assertSee('محاسبه سود');
    }

    public function test_storefront_subscription_detail_page_loads(): void
    {
        $plan = \App\Models\SubscriptionPlan::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'پلن اقتصادی ۳۰ روزه',
            'duration_days' => 30,
            'total_items' => 20,
            'daily_limit' => 1,
            'price' => 500000,
            'status' => 'active',
        ]);

        $response = $this->get(route('storefront.subscriptions.show', $plan));
        $response->assertStatus(200);
        $response->assertSee('پلن اقتصادی ۳۰ روزه');
        $response->assertSee('500,000 تومان');
        $response->assertSee('افزودن این اشتراک به سبد خرید');
    }

    public function test_storefront_about_page_loads_with_hours_and_map(): void
    {
        $response = $this->get(route('storefront.about'));
        $response->assertStatus(200);
        $response->assertSee('داستان بی کافه؛ دانه باکیفیت');
        $response->assertSee('شیراز، میدان ارم');
        $response->assertSee('about-cafe-map');
    }

    public function test_admin_orders_show_includes_map_coordinates(): void
    {
        $customer = Customer::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'مشتری پیک',
            'phone' => '09170001122',
        ]);

        $address = \App\Models\CustomerAddress::create([
            'customer_id' => $customer->id,
            'title' => 'خانه',
            'address_line' => 'شیراز، خیابان ملاصدرا، کوچه ۲',
            'latitude' => 29.6250000,
            'longitude' => 52.5150000,
        ]);

        $order = Order::create([
            'cafe_id' => $this->cafe->id,
            'customer_id' => $customer->id,
            'delivery_address_id' => $address->id,
            'order_number' => 'BC-MAP-01',
            'type' => 'normal',
            'order_type' => 'courier',
            'fulfillment_type' => 'delivery',
            'status' => 'pending',
            'total_price' => 95000,
            'final_payable' => 95000,
        ]);

        $response = $this->actingAs($this->admin)->get(route('orders.show', $order));
        $response->assertStatus(200);
        $response->assertSee('29.625');
        $response->assertSee('52.515');
        $response->assertSee('order-admin-map');
    }

    public function test_admin_can_access_receipt_settings_designer_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('settings.receipt.edit'));
        $response->assertStatus(200);
        $response->assertSee('شخصی‌سازی کامل فاکتور و فیش چاپگر حرارتی');
        $response->assertSee('پیش‌نمایش زنده فیش حرارتی');
        $response->assertSee('قالب، کاغذ و تایپوگرافی حرفه‌ای');
    }

    public function test_admin_can_update_receipt_settings_and_font(): void
    {
        $response = $this->actingAs($this->admin)->post(route('settings.receipt.update'), [
            'paper_width' => '80mm',
            'font_family' => 'shabnam',
            'font_size' => 'large',
            'header_title' => 'کافه رستوران تخصصی بی شیراز',
            'header_subtitle' => 'میدان ارم، ابتدای بلوار دانشجو',
            'invoice_title' => 'فیش سفارش مشتری',
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
            'tax_percent' => '9',
            'show_amount_in_words' => '1',
            'show_payment_method' => '1',
            'show_payment_status' => '1',
            'show_barcode' => '1',
            'show_qr_code' => '1',
            'qr_code_type' => 'website',
            'footer_message' => 'نوش جان! از همراهی شما با بی کافه سپاسگزاریم.',
            'show_wifi' => '1',
            'wifi_ssid' => 'BlueCup-VIP',
            'wifi_password' => 'shiraz2026',
            'show_instagram' => '1',
            'instagram_handle' => '@bcafe.shiraz',
            'auto_print' => '0',
        ]);

        $response->assertRedirect(route('settings.receipt.edit'));
        $response->assertSessionHas('success');

        $setting = \App\Models\ReceiptSetting::getForCafe($this->cafe->id);
        $this->assertEquals('shabnam', $setting->font_family);
        $this->assertEquals('large', $setting->font_size);
        $this->assertEquals('کافه رستوران تخصصی بی شیراز', $setting->header_title);
        $this->assertEquals('BlueCup-VIP', $setting->wifi_ssid);
        $this->assertFalse($setting->auto_print);
    }

    public function test_thermal_receipt_applies_customized_settings_and_font(): void
    {
        $setting = \App\Models\ReceiptSetting::getForCafe($this->cafe->id);
        $setting->update([
            'font_family' => 'iransans',
            'font_size' => 'compact',
            'header_title' => 'بی کافه شعبه مرکزی ارم',
            'header_subtitle' => 'بار تخصصی قهوه اسپشالتی',
            'invoice_title' => 'صورت‌حساب نهایی فروش',
            'show_wifi' => true,
            'wifi_ssid' => 'Karasu-5G',
            'wifi_password' => 'coffee2026',
        ]);

        $order = Order::create([
            'cafe_id' => $this->cafe->id,
            'order_number' => 'BC-PRINT-CUSTOM',
            'type' => 'normal',
            'order_type' => 'dine_in',
            'table_number' => '7',
            'status' => 'completed',
            'total_price' => 140000,
            'final_payable' => 140000,
        ]);

        $response = $this->actingAs($this->admin)->get(route('orders.receipt', $order));
        $response->assertStatus(200);
        $response->assertSee('بی کافه شعبه مرکزی ارم');
        $response->assertSee('صورت‌حساب نهایی فروش');
        $response->assertSee('Karasu-5G');
        $response->assertSee('coffee2026');
        $this->assertStringContainsString("'IRANSans'", $response->getContent());
    }

    public function test_card_receipt_cleanup_artisan_command_deletes_older_receipts_and_preserves_recent(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        // Create an old receipt file (8 days old)
        $oldFile = 'receipts/card_old_sample.jpg';
        \Illuminate\Support\Facades\Storage::disk('public')->put($oldFile, 'fake receipt content old');

        // Create a recent receipt file (2 days old)
        $recentFile = 'receipts/card_recent_sample.jpg';
        \Illuminate\Support\Facades\Storage::disk('public')->put($recentFile, 'fake receipt content recent');

        // Create orders for payments
        $orderOld = Order::create([
            'cafe_id' => $this->cafe->id,
            'order_number' => 'BC-CLEAN-OLD',
            'type' => 'normal',
            'order_type' => 'takeaway',
            'status' => 'pending',
            'total_price' => 150000,
            'final_payable' => 150000,
        ]);

        $orderRecent = Order::create([
            'cafe_id' => $this->cafe->id,
            'order_number' => 'BC-CLEAN-REC',
            'type' => 'normal',
            'order_type' => 'takeaway',
            'status' => 'pending',
            'total_price' => 80000,
            'final_payable' => 80000,
        ]);

        // Create payment records
        $oldPayment = \App\Models\Payment::create([
            'order_id' => $orderOld->id,
            'amount' => 150000,
            'gateway' => 'card_to_card',
            'status' => 'pending',
            'receipt_image' => $oldFile,
        ]);
        \Illuminate\Support\Facades\DB::table('payments')->where('id', $oldPayment->id)->update([
            'created_at' => now()->subDays(8),
            'updated_at' => now()->subDays(8),
        ]);

        $recentPayment = \App\Models\Payment::create([
            'order_id' => $orderRecent->id,
            'amount' => 80000,
            'gateway' => 'card_to_card',
            'status' => 'pending',
            'receipt_image' => $recentFile,
        ]);
        \Illuminate\Support\Facades\DB::table('payments')->where('id', $recentPayment->id)->update([
            'created_at' => now()->subDays(2),
            'updated_at' => now()->subDays(2),
        ]);

        $this->artisan('receipts:cleanup-card', ['--days' => 7])
            ->assertExitCode(0);

        // Old file must be deleted from storage and cleared from DB
        \Illuminate\Support\Facades\Storage::disk('public')->assertMissing($oldFile);
        $oldPayment->refresh();
        $this->assertNull($oldPayment->receipt_image);

        // Recent file must be preserved
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($recentFile);
        $recentPayment->refresh();
        $this->assertEquals($recentFile, $recentPayment->receipt_image);
    }

    public function test_admin_can_manage_media_alt_text_and_delete(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $uploaded = \Illuminate\Http\UploadedFile::fake()->image('latte-art.jpg', 600, 600);

        $response = $this->actingAs($this->admin)->post(route('admin.media.store'), [
            'files' => [$uploaded],
            'alt_text' => 'لاته آرت تخصصی بی کافه',
            'title' => 'عنوان لاته آرت',
        ]);

        $response->assertRedirect(route('admin.media.index'));

        $mediaItem = \App\Models\MediaItem::where('alt_text', 'لاته آرت تخصصی بی کافه')->first();
        $this->assertNotNull($mediaItem);
        $this->assertEquals('عنوان لاته آرت', $mediaItem->title);

        // Update alt text
        $updateResp = $this->actingAs($this->admin)->put(route('admin.media.update', $mediaItem), [
            'alt_text' => 'متن سئو بروزرسانی شده',
            'title' => 'عنوان جدید',
        ]);
        $updateResp->assertRedirect(route('admin.media.index'));

        $mediaItem->refresh();
        $this->assertEquals('متن سئو بروزرسانی شده', $mediaItem->alt_text);
        $this->assertEquals('عنوان جدید', $mediaItem->title);

        // List JSON endpoint
        $jsonResp = $this->actingAs($this->admin)->get(route('admin.media.list-json'));
        $jsonResp->assertStatus(200);
        $jsonResp->assertJsonFragment(['name' => $mediaItem->name]);

        // Delete media
        $delResp = $this->actingAs($this->admin)->delete(route('admin.media.destroy'), [
            'id' => $mediaItem->id,
            'path' => $mediaItem->path,
        ]);
        $delResp->assertRedirect(route('admin.media.index'));

        $this->assertNull(\App\Models\MediaItem::find($mediaItem->id));
    }

    public function test_admin_can_update_product_image_and_remove_image(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $category = \App\Models\Category::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'نوشیدنی گرم',
        ]);

        $product = Product::create([
            'cafe_id' => $this->cafe->id,
            'category_id' => $category->id,
            'name' => 'کاپوچینو تست',
            'type' => 'نوشیدنی گرم',
            'price' => 75000,
            'image' => '/storage/products/initial.jpg',
            'status' => 'active',
        ]);

        // Upload replacement image
        $newFile = \Illuminate\Http\UploadedFile::fake()->image('new-cappuccino.jpg');
        $response = $this->actingAs($this->admin)->put(route('products.update', $product), [
            'name' => 'کاپوچینو تست بروز',
            'category_id' => $category->id,
            'price' => 75000,
            'status' => 'active',
            'image_file' => $newFile,
        ]);

        $response->assertRedirect(route('products.index'));
        $product->refresh();
        $this->assertNotEquals('/storage/products/initial.jpg', $product->image);
        $this->assertStringStartsWith('/storage/products/', $product->image);

        // Remove image completely
        $removeResp = $this->actingAs($this->admin)->put(route('products.update', $product), [
            'name' => 'کاپوچینو تست بدون عکس',
            'category_id' => $category->id,
            'price' => 75000,
            'status' => 'active',
            'remove_image' => '1',
        ]);

        $removeResp->assertRedirect(route('products.index'));
        $product->refresh();
        $this->assertNull($product->image);
    }

    public function test_live_kitchen_feed_returns_card_receipt_details(): void
    {
        $order = Order::create([
            'cafe_id' => $this->cafe->id,
            'order_number' => 'BC-FEED-001',
            'type' => 'normal',
            'order_type' => 'takeaway',
            'status' => 'pending',
            'payment_method' => 'card_to_card',
            'payment_status' => 'pending',
            'total_price' => 90000,
            'final_payable' => 90000,
        ]);

        \App\Models\Payment::create([
            'order_id' => $order->id,
            'amount' => 90000,
            'gateway' => 'card_to_card',
            'status' => 'pending',
            'receipt_image' => 'receipts/test_feed_receipt.jpg',
            'card_pan' => '6037-9918-1234-5678',
            'tracking_number' => 'REF-987654321',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.live-orders.feed'));
        $response->assertStatus(200);

        $json = $response->json();
        $this->assertNotEmpty($json['orders']);
        $feedOrder = collect($json['orders'])->firstWhere('id', $order->id);
        $this->assertNotNull($feedOrder);
        $this->assertStringContainsString('receipts/test_feed_receipt.jpg', $feedOrder['receipt_image']);
        $this->assertEquals('6037-9918-1234-5678', $feedOrder['card_pan']);
        $this->assertEquals('REF-987654321', $feedOrder['tracking_number']);
    }
}

