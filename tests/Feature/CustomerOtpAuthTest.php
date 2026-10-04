<?php

namespace Tests\Feature;

use App\Models\Cafe;
use App\Models\CustomerOtp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class CustomerOtpAuthTest extends TestCase
{
    use RefreshDatabase;

    protected Cafe $cafe;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cafe = Cafe::create([
            'name' => 'تست کافه',
            'phone' => '02111111111',
        ]);
    }

    public function test_can_send_otp_code_successfully(): void
    {
        $response = $this->postJson('/api/auth/send-otp', [
            'phone' => '09129999999',
            'cafe_id' => $this->cafe->id,
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('customer_otps', [
            'phone' => '09129999999',
        ]);
    }

    public function test_can_verify_valid_otp_code(): void
    {
        $phone = '09128888888';
        $code = '123456';

        CustomerOtp::create([
            'phone' => $phone,
            'code' => $code,
            'expires_at' => Carbon::now()->addMinutes(5),
        ]);

        $response = $this->postJson('/api/auth/verify-otp', [
            'phone' => $phone,
            'code' => $code,
            'cafe_id' => $this->cafe->id,
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure(['data' => ['token', 'customer']]);

        $token = $response->json('data.token');
        $this->assertNotEmpty($token);

        $this->assertDatabaseHas('customers', [
            'phone' => $phone,
        ]);

        // Verify protected API route works with the Sanctum token
        $authResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/loyalty/points');
        $authResponse->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_unauthenticated_request_cannot_access_loyalty_points(): void
    {
        $response = $this->getJson('/api/loyalty/points');
        $response->assertStatus(401);
    }

    public function test_staff_cannot_access_reports_and_admin_can(): void
    {
        $staff = \App\Models\User::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'پرسنل',
            'phone' => '09121111111',
            'email' => 'staff@karasu.test',
            'password' => bcrypt('password'),
            'role' => 'staff',
        ]);

        $admin = \App\Models\User::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'مدیر',
            'phone' => '09122222222',
            'email' => 'admin@karasu.test',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Staff should be forbidden (403)
        $this->actingAs($staff)->get('/reports')->assertStatus(403);

        // Admin should have access (200)
        $this->actingAs($admin)->get('/reports')->assertStatus(200);
    }
}
