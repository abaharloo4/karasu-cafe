<?php

namespace Tests\Feature;

use App\Models\Cafe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_application_routes_render_cleanly_with_design_tokens(): void
    {
        $cafe = Cafe::create(['name' => 'بی کافه شیراز']);
        $user = User::create([
            'cafe_id' => $cafe->id,
            'name' => 'مدیر کافه',
            'email' => 'admin@bcafe.local',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Storefront Menu at root /
        $storefrontResponse = $this->get('/');
        $storefrontResponse->assertStatus(200);
        $storefrontResponse->assertSee('بی کافه');

        // Admin Dashboard at /admin
        $dashboardResponse = $this->actingAs($user)->get('/admin');
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('بی کافه');
        $dashboardResponse->assertSee('میز کار شیفت جاری کافه');

        // Daily Usage / POS
        $posResponse = $this->actingAs($user)->get('/daily-usage');
        $posResponse->assertStatus(200);
        $posResponse->assertSee('صندوق فروش و تحویل سفارشات');

        // Kitchen Live Board
        $liveResponse = $this->actingAs($user)->get('/admin/live-orders');
        $liveResponse->assertStatus(200);
        $liveResponse->assertSee('تابلوی زنده سفارشات بی کافه');

        // Orders Index
        $ordersResponse = $this->actingAs($user)->get('/orders');
        $ordersResponse->assertStatus(200);
        $ordersResponse->assertSee('تاریخچه و دفتر سفارش‌ها');
    }
}