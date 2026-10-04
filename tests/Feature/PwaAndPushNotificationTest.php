<?php

namespace Tests\Feature;

use App\Jobs\SendPushNotificationJob;
use App\Models\Cafe;
use App\Models\Customer;
use App\Models\NotificationPreference;
use App\Models\PushSubscription;
use App\Models\PwaDailyStat;
use App\Models\PwaEvent;
use App\Models\PwaSetting;
use App\Models\User;
use App\Services\Pwa\PushNotificationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class PwaAndPushNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected Cafe $cafe;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cafe = Cafe::firstOrCreate(
            ['id' => 1],
            ['name' => 'بی کافه (B Cafe)', 'phone' => '07136280000', 'address' => 'شیراز، میدان ارم']
        );

        PwaSetting::seedDefaults();
    }

    public function test_dynamic_manifest_returns_correct_json_per_panel()
    {
        $response = $this->get('/manifest.webmanifest');
        $response->assertOk()
            ->assertHeader('Content-Type', 'application/manifest+json; charset=utf-8')
            ->assertJsonPath('short_name', 'بی کافه')
            ->assertJsonPath('start_url', '/')
            ->assertJsonPath('dir', 'rtl');

        $adminManifest = $this->get('/manifest.webmanifest?panel=admin');
        $adminManifest->assertOk()
            ->assertJsonPath('short_name', 'بی کافه')
            ->assertJsonPath('start_url', '/');
    }

    public function test_vapid_public_key_endpoint()
    {
        $response = $this->getJson('/pwa/vapid-public-key');
        $response->assertOk()
            ->assertJsonStructure(['publicKey']);

        $this->assertNotEmpty($response->json('publicKey'));
    }

    public function test_authenticated_customer_can_subscribe_and_unsubscribe_push()
    {
        $customer = Customer::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'مشتری تستی',
            'phone' => '09171112233',
        ]);

        $subData = [
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/fake-test-endpoint-123',
            'keys' => [
                'p256dh' => 'BNcRdreALRFXTkOOUHK1EtK2wtaz5Ry4YfYCA_0QT9t0PPM6E00ZQQ23',
                'auth' => 'tBHItDaA9xOm0UJFs3GASw',
            ],
            'panel' => 'customer',
        ];

        // Must be authenticated
        $guestRes = $this->postJson('/pwa/subscribe', $subData);
        $guestRes->assertStatus(401);

        // Authenticated customer
        $authRes = $this->actingAs($customer, 'customer')->postJson('/pwa/subscribe', $subData);
        $authRes->assertOk()->assertJsonPath('success', true);

        $this->assertDatabaseHas('push_subscriptions', [
            'subscribable_type' => $customer->getMorphClass(),
            'subscribable_id' => $customer->id,
            'panel' => 'customer',
        ]);

        // Unsubscribe
        $unsubRes = $this->actingAs($customer, 'customer')->postJson('/pwa/unsubscribe', [
            'endpoint' => $subData['endpoint'],
        ]);
        $unsubRes->assertOk();

        $this->assertDatabaseMissing('push_subscriptions', [
            'endpoint' => $subData['endpoint'],
        ]);
    }

    public function test_pwa_telemetry_event_logging()
    {
        $response = $this->postJson('/pwa/event', [
            'event' => 'page_view',
            'panel' => 'customer',
            'path' => '/menu',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('pwa_events', [
            'event_type' => 'page_view',
            'panel' => 'customer',
            'path' => '/menu',
        ]);
    }

    public function test_customer_notification_preferences_defaults_and_updates()
    {
        $customer = Customer::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'مشتری دو',
            'phone' => '09179998877',
        ]);

        // Transactional is ON by default; Marketing is OFF by default
        $this->assertTrue(NotificationPreference::isEnabled($customer, 'order_status'));
        $this->assertTrue(NotificationPreference::isEnabled($customer, 'rewards'));
        $this->assertFalse(NotificationPreference::isEnabled($customer, 'marketing'));

        // Customer updates preference to opt into marketing
        $res = $this->actingAs($customer, 'customer')->post('/panel/notifications', [
            'order_status' => '1',
            'rewards' => '1',
            'marketing' => '1',
        ]);

        $res->assertRedirect();
        $this->assertTrue(NotificationPreference::isEnabled($customer, 'marketing'));
    }



    public function test_admin_can_update_pwa_settings()
    {
        $admin = User::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'مدیر کافه',
            'email' => 'admin_pwa@bcafe.ir',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $updateData = [
            'panel' => 'customer',
            'app_name' => 'بی کافه تخصصی',
            'short_name' => 'بی کافه',
            'theme_color' => '#123456',
            'background_color' => '#654321',
            'ga4_measurement_id' => 'G-ABC1234567',
            'push_enabled' => '1',
            'allowed_categories' => ['order_status', 'rewards'],
        ];

        $res = $this->actingAs($admin)->post('/admin/pwa-settings', $updateData);
        $res->assertRedirect();

        $setting = PwaSetting::forPanel('customer');
        $this->assertEquals('بی کافه تخصصی', $setting->app_name);
        $this->assertEquals('#123456', $setting->theme_color);
        $this->assertEquals('G-ABC1234567', $setting->ga4_measurement_id);
        $this->assertEquals(['order_status', 'rewards'], $setting->allowed_categories);
    }

    public function test_pwa_analytics_is_safely_removed()
    {
        $admin = User::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'مدیر کافه ۲',
            'email' => 'admin_pwa2@bcafe.ir',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $analyticsRes = $this->actingAs($admin)->get('/admin/pwa-analytics');
        $analyticsRes->assertNotFound();
    }

    public function test_push_service_respects_killswitch_and_dispatches_job()
    {
        Queue::fake();

        $customer = Customer::create([
            'cafe_id' => $this->cafe->id,
            'name' => 'مشتری ۳',
            'phone' => '09177778899',
        ]);

        // Add a push subscription
        PushSubscription::create([
            'subscribable_type' => $customer->getMorphClass(),
            'subscribable_id' => $customer->id,
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/test-sub-1',
            'endpoint_hash' => PushSubscription::hashEndpoint('https://fcm.googleapis.com/fcm/send/test-sub-1'),
            'p256dh' => 'fake-key',
            'auth' => 'fake-auth',
            'panel' => 'customer',
        ]);

        $pushService = app(PushNotificationService::class);

        // 1. Allowed category
        $count = $pushService->send($customer, 'تست', 'متن تست', [], 'order_status');
        $this->assertEquals(1, $count);
        Queue::assertPushed(SendPushNotificationJob::class);

        // 2. Disabled category (Marketing is off by default)
        $countMarketing = $pushService->send($customer, 'تخفیف', 'تخفیف ویژه', [], 'marketing');
        $this->assertEquals(0, $countMarketing);

        // 3. Global Kill-switch
        $setting = PwaSetting::forPanel('customer');
        $setting->update([
            'notification_killswitches' => [
                'push_enabled' => false,
                'allowed_categories' => [],
            ],
        ]);

        $countKillswitch = $pushService->send($customer, 'تست بعد از خاموشی', 'متن', [], 'order_status');
        $this->assertEquals(0, $countKillswitch);
    }

    public function test_daily_stat_aggregation_command()
    {
        // Insert events for today
        PwaEvent::create([
            'panel' => 'customer',
            'event_type' => 'install',
            'path' => '/',
        ]);

        PwaEvent::create([
            'panel' => 'customer',
            'event_type' => 'page_view',
            'path' => '/menu',
        ]);

        $dateStr = Carbon::today()->toDateString();

        $this->artisan('pwa:aggregate-daily', ['--date' => $dateStr])
            ->assertSuccessful();

        $stat = PwaDailyStat::where('date', $dateStr)
            ->where('panel', 'customer')
            ->first();

        $this->assertNotNull($stat);
        $this->assertEquals(1, $stat->installs_count);
        $this->assertEquals(1, $stat->page_views_count);
    }
}
