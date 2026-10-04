<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. PWA Settings per panel (customer, admin, driver)
        Schema::create('pwa_settings', function (Blueprint $table) {
            $table->id();
            $table->string('panel', 30)->unique(); // customer, admin, driver
            $table->string('app_name');
            $table->string('short_name');
            $table->string('theme_color', 20)->default('#387780');
            $table->string('background_color', 20)->default('#0F172A');
            $table->string('icon_192')->nullable();
            $table->string('icon_512')->nullable();
            $table->string('icon_maskable')->nullable();
            $table->string('start_url')->default('/');
            $table->string('scope')->default('/');
            $table->string('ga4_measurement_id', 40)->nullable();
            $table->json('notification_killswitches')->nullable();
            $table->timestamps();
        });

        // Seed initial default PWA settings for all 3 panels
        DB::table('pwa_settings')->insert([
            [
                'panel' => 'customer',
                'app_name' => 'بی کافه | قهوه تخصصی شیراز',
                'short_name' => 'بی کافه',
                'theme_color' => '#387780',
                'background_color' => '#0F172A',
                'icon_192' => null,
                'icon_512' => null,
                'icon_maskable' => null,
                'start_url' => '/',
                'scope' => '/',
                'ga4_measurement_id' => null,
                'notification_killswitches' => json_encode([
                    'order_status' => true,
                    'card_verification' => true,
                    'loyalty' => true,
                    'marketing' => true,
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'panel' => 'admin',
                'app_name' => 'پنل مدیریت و آشپزخانه بی کافه',
                'short_name' => 'مدیریت کافه',
                'theme_color' => '#141D22',
                'background_color' => '#0F172A',
                'icon_192' => null,
                'icon_512' => null,
                'icon_maskable' => null,
                'start_url' => '/admin',
                'scope' => '/admin/',
                'ga4_measurement_id' => null,
                'notification_killswitches' => json_encode([
                    'new_order' => true,
                    'card_verification_pending' => true,
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 2. Polymorphic Push Subscriptions (Customer, User, Driver)
        Schema::create('push_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('subscribable_type');
            $table->unsignedBigInteger('subscribable_id');
            $table->index(['subscribable_type', 'subscribable_id']);
            $table->text('endpoint');
            $table->string('endpoint_hash', 64)->unique();
            $table->text('p256dh');
            $table->string('auth');
            $table->string('panel', 30)->default('customer')->index(); // customer, admin, driver
            $table->string('user_agent')->nullable();
            $table->timestamps();
        });

        // 3. Granular Notification Preferences per Subscriber
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->string('subscribable_type');
            $table->unsignedBigInteger('subscribable_id');
            $table->string('category', 50); // order_status, card_verification, loyalty, marketing, new_order, driver_assignment, etc.
            $table->boolean('enabled')->default(true);
            $table->unique(['subscribable_type', 'subscribable_id', 'category'], 'sub_pref_unique');
            $table->timestamps();
        });

        // 4. Lightweight PWA Events for Analytics
        Schema::create('pwa_events', function (Blueprint $table) {
            $table->id();
            $table->string('panel', 30)->index();
            $table->string('event_type', 40)->index(); // install, page_view, notification_sent, notification_opened, subscription_created, subscription_revoked
            $table->string('subscribable_type')->nullable();
            $table->unsignedBigInteger('subscribable_id')->nullable();
            $table->string('path')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('created_at')->index();
        });

        // 5. Daily Aggregated PWA Rollup Stats
        Schema::create('pwa_daily_stats', function (Blueprint $table) {
            $table->id();
            $table->string('panel', 30);
            $table->date('date');
            $table->unsignedInteger('installs_count')->default(0);
            $table->unsignedInteger('active_subscribers_count')->default(0);
            $table->unsignedInteger('page_views_count')->default(0);
            $table->unsignedInteger('notifications_sent_count')->default(0);
            $table->unsignedInteger('notifications_opened_count')->default(0);
            $table->json('page_breakdown')->nullable();
            $table->unique(['panel', 'date']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pwa_daily_stats');
        Schema::dropIfExists('pwa_events');
        Schema::dropIfExists('notification_preferences');
        Schema::dropIfExists('push_subscriptions');
        Schema::dropIfExists('pwa_settings');
    }
};
