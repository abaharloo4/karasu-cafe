<?php

namespace Tests\Feature;

use App\Models\Cafe;
use App\Models\Deployment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DeploymentAndUpdateSystemTest extends TestCase
{
    use RefreshDatabase;

    protected Cafe $cafe;
    protected User $admin;
    protected User $staff;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cafe = Cafe::create([
            'name' => 'بی کافه',
            'slug' => 'b-cafe',
            'status' => 'active',
        ]);

        $this->admin = User::factory()->create([
            'cafe_id' => $this->cafe->id,
            'role' => 'admin',
        ]);

        $this->staff = User::factory()->create([
            'cafe_id' => $this->cafe->id,
            'role' => 'staff',
        ]);
    }

    public function test_guest_is_redirected_from_deployments_page(): void
    {
        $response = $this->get('/admin/deployments');
        $response->assertRedirect('/login');
    }

    public function test_staff_cannot_access_deployments_page(): void
    {
        $response = $this->actingAs($this->staff)->get('/admin/deployments');
        $response->assertStatus(403);
    }

    public function test_admin_can_view_deployments_dashboard(): void
    {
        Deployment::create([
            'cafe_id' => $this->cafe->id,
            'commit_sha' => 'c7b912a456789012345678901234567890123456',
            'commit_message' => 'پیاده‌سازی سیستم استقرار امن',
            'commit_author' => 'Amir',
            'started_at' => Carbon::now()->subMinutes(10),
            'finished_at' => Carbon::now()->subMinutes(9),
            'status' => 'success',
            'log' => 'Deployment Completed Successfully',
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/deployments');

        $response->assertStatus(200);
        $response->assertSee('مدیریت و استقرار خودکار نسخه‌ها');
        $response->assertSee('بررسی آپدیت');
        $response->assertSee('اجرای آپدیت');
        $response->assertSee('c7b912a');
        $response->assertSee('پیاده‌سازی سیستم استقرار امن');
    }

    public function test_check_updates_endpoint_returns_json(): void
    {
        Http::fake([
            'api.github.com/repos/*/commits/main' => Http::response([
                'sha' => '1122334455667788990011223344556677889900',
                'commit' => [
                    'message' => 'کامیت تست برای بررسی آپدیت',
                    'author' => [
                        'name' => 'GitHub Tester',
                        'date' => Carbon::now()->toIso8601String(),
                    ],
                ],
                'html_url' => 'https://github.com/abaharloo4/karasu-cafe/commit/1122334',
            ], 200),
            'api.github.com/repos/*/commits?*' => Http::response([], 200),
        ]);

        $response = $this->actingAs($this->admin)->postJson('/admin/deployments/check');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
    }

    public function test_trigger_deployment_creates_running_record(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/admin/deployments/trigger', [
            'commit_sha' => 'aabbccddeeff00112233445566778899aabbccdd',
            'commit_message' => 'تست اجرای استقرار',
            'commit_author' => 'Admin User',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('deployments', [
            'commit_sha' => 'aabbccddeeff00112233445566778899aabbccdd',
            'commit_message' => 'تست اجرای استقرار',
            'commit_author' => 'Admin User',
            'triggered_by' => $this->admin->id,
        ]);
    }

    public function test_deployment_status_polling(): void
    {
        $deployment = Deployment::create([
            'cafe_id' => $this->cafe->id,
            'commit_sha' => '1234567890abcdef1234567890abcdef12345678',
            'commit_message' => 'تست پولینگ وضعیت',
            'started_at' => Carbon::now(),
            'status' => 'running',
            'log' => 'در حال دریافت فایل‌ها...',
        ]);

        $response = $this->actingAs($this->admin)->getJson("/admin/deployments/{$deployment->id}/status");

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'running',
            'log' => 'در حال دریافت فایل‌ها...',
        ]);
    }

    public function test_opcache_clear_route_requires_valid_key(): void
    {
        config(['deployment.deploy_secret' => 'super-secret-key-123']);

        // Missing or wrong key
        $response = $this->getJson('/system/opcache-clear?key=wrong-key');
        $response->assertStatus(403);

        // Correct key
        $response = $this->getJson('/system/opcache-clear?key=super-secret-key-123');
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    public function test_health_check_up_route_returns_ok(): void
    {
        $response = $this->get('/up');
        $response->assertStatus(200);
    }

    public function test_artisan_record_success_command(): void
    {
        $deployment = Deployment::create([
            'cafe_id' => $this->cafe->id,
            'commit_sha' => 'abcdef1234567890abcdef1234567890abcdef12',
            'started_at' => Carbon::now()->subSeconds(20),
            'status' => 'running',
        ]);

        $this->artisan('deployment:record-success', [
            '--commit' => 'abcdef1234567890abcdef1234567890abcdef12',
            '--message' => 'مستقر شد با موفقیت',
            '--author' => 'Deploy Bot',
        ])->assertSuccessful();

        $deployment->refresh();
        $this->assertSame('success', $deployment->status);
        $this->assertNotNull($deployment->finished_at);
        $this->assertSame('مستقر شد با موفقیت', $deployment->commit_message);
    }
}
