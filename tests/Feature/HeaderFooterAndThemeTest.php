<?php

namespace Tests\Feature;

use App\Models\Cafe;
use App\Models\Customer;
use App\Models\SiteContentSetting;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HeaderFooterAndThemeTest extends TestCase
{
    use RefreshDatabase;

    protected Cafe $cafe;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cafe = Cafe::create([
            'name' => 'بی کافه (B Cafe)',
            'address' => 'شیراز، میدان ارم، ابتدای بلوار دانشجو',
            'phone' => '07136280000',
            'is_active' => true,
        ]);

        SiteContentSetting::create([
            'cafe_id' => $this->cafe->id,
            'footer_text' => 'بی کافه شیراز، فنجان روزمرگی شما',
            'contact_phone' => '۰۷۱-۳۶۲۸۰۰۰۰',
            'address' => 'شیراز، میدان ارم، ابتدای بلوار دانشجو',
        ]);
    }

    public function test_storefront_menu_renders_unified_header_and_footer_and_theme_init(): void
    {
        $response = $this->get(route('storefront.menu'));
        $response->assertStatus(200);

        // Verify Theme Init Script & Dark Mode Toggle are present
        $response->assertSee('bcafe_theme', false);
        $response->assertSee('toggleDarkMode', false);

        // Verify Unified Header Component & Brand Links
        $response->assertSee('بی کافه (B Cafe)');
        $response->assertSee('شیراز • بار تخصصی قهوه');
        $response->assertSee(route('storefront.subscriptions.index'));
        $response->assertSee(route('storefront.about'));

        // Verify Unified Footer Component
        $response->assertSee('اطلاعات شعبه شیراز');
        $response->assertSee('طراحی و توسعه توسط امیرمحمد بهارلو');
    }

    public function test_storefront_about_renders_unified_header_and_footer(): void
    {
        $response = $this->get(route('storefront.about'));
        $response->assertStatus(200);

        $response->assertSee('toggleDarkMode', false);
        $response->assertSee('اطلاعات شعبه شیراز');
        $response->assertSee('طراحی و توسعه توسط امیرمحمد بهارلو');
    }

    public function test_storefront_subscriptions_renders_unified_header_and_footer(): void
    {
        $plan = SubscriptionPlan::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'پلن هفتگی قهوه',
            'price' => 200000,
            'duration_days' => 7,
            'total_items' => 5,
            'daily_limit' => 1,
            'is_active' => true,
        ]);

        $response = $this->get(route('storefront.subscriptions.index'));
        $response->assertStatus(200);

        $response->assertSee('toggleDarkMode', false);
        $response->assertSee('اطلاعات شعبه شیراز');
        $response->assertSee('طراحی و توسعه توسط امیرمحمد بهارلو');

        // Check subscription details page
        $detailResponse = $this->get(route('storefront.subscriptions.show', $plan));
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('toggleDarkMode', false);
        $detailResponse->assertSee('طراحی و توسعه توسط امیرمحمد بهارلو');
    }

    public function test_cart_page_renders_unified_header_and_footer(): void
    {
        $response = $this->get(route('cart.index'));
        $response->assertStatus(200);

        $response->assertSee('toggleDarkMode', false);
        $response->assertSee('طراحی و توسعه توسط امیرمحمد بهارلو');
    }

    public function test_admin_dashboard_renders_theme_toggle(): void
    {
        $admin = User::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'مدیر کافه',
            'email' => 'admin@bcafe.ir',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get(route('dashboard'));
        $response->assertStatus(200);

        $response->assertSee('toggleDarkMode', false);
        $response->assertSee('bcafe_theme', false);
    }

    public function test_customer_portal_renders_theme_toggle(): void
    {
        $customer = Customer::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'رضا شیرازی',
            'phone' => '09171112233',
        ]);

        $response = $this->actingAs($customer, 'customer')->get(route('customer.dashboard'));
        $response->assertStatus(200);

        $response->assertSee('toggleDarkMode', false);
        $response->assertSee('bcafe_theme', false);
    }
}
