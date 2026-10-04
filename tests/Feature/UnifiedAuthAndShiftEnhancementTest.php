<?php

namespace Tests\Feature;

use App\Models\Cafe;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UnifiedAuthAndShiftEnhancementTest extends TestCase
{
    use RefreshDatabase;

    protected Cafe $cafe;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cafe = Cafe::firstOrCreate(
            ['id' => 1],
            [
                'name' => 'بی کافه (B Cafe)',
                'phone' => '07136280000',
                'address' => 'شیراز، ستارخان',
                'order_start_time' => '08:00',
                'order_end_time' => '22:00',
                'is_ordering_open' => true,
            ]
        );
    }

    public function test_unified_login_page_renders_successfully()
    {
        $response = $this->get('/login');
        $response->assertOk()
            ->assertSee('بی کافه (B Cafe)')
            ->assertSee('ورود به حساب کاربری')
            ->assertSee('کد پیامکی')
            ->assertSee('کلمه عبور');

        // Customer login route also points to the same unified view
        $customerLoginResponse = $this->get('/customer/login');
        $customerLoginResponse->assertOk()
            ->assertSee('بی کافه (B Cafe)');
    }

    public function test_staff_login_with_password_redirects_to_dashboard()
    {
        $admin = User::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'ادمین ارشد',
            'email' => 'admin@bcafe.ir',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
        ]);

        $response = $this->post('/login', [
            'login' => 'admin@bcafe.ir',
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($admin, 'web');
    }

    public function test_cafe_operating_hours_settings_can_be_updated()
    {
        $admin = User::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'مدیر کافه',
            'email' => 'manager@bcafe.ir',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->put(route('settings.cafe.update'), [
            'order_start_time' => '09:00',
            'order_end_time' => '23:30',
            'is_ordering_open' => 1,
            'phone' => '07136360000',
            'address' => 'شیراز، ستارخان',
            'closed_message' => 'کافه بسته است.',
        ]);

        $response->assertRedirect(route('settings.cafe.edit'));
        $this->cafe->refresh();
        $this->assertEquals('09:00', $this->cafe->order_start_time);
        $this->assertEquals('23:30', $this->cafe->order_end_time);
        $this->assertTrue($this->cafe->is_ordering_open);
    }

    public function test_order_fulfillment_labels_and_in_person_behavior()
    {
        $dineInOrder = new Order([
            'order_type' => 'dine_in',
            'table_number' => '5',
        ]);
        $this->assertTrue($dineInOrder->isDineIn());
        $this->assertFalse($dineInOrder->isDelivery());
        $this->assertEquals('سرو در سالن (حضوری) - میز 5', $dineInOrder->getFulfillmentLabel());

        $takeawayOrder = new Order([
            'order_type' => 'takeaway',
            'fulfillment_type' => 'pickup',
        ]);
        $this->assertTrue($takeawayOrder->isTakeaway());
        $this->assertFalse($takeawayOrder->isDelivery());
        $this->assertEquals('بیرون‌بر (تحویل حضوری)', $takeawayOrder->getFulfillmentLabel());

        $deliveryOrder = new Order([
            'order_type' => 'courier',
            'fulfillment_type' => 'delivery',
        ]);
        $this->assertTrue($deliveryOrder->isDelivery());
        $this->assertEquals('ارسال با پیک (شیراز)', $deliveryOrder->getFulfillmentLabel());
    }
}
