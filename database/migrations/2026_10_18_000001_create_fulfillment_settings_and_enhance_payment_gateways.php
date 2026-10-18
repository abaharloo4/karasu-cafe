<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Fulfillment / Shipping / Receiving Methods Table
        if (!Schema::hasTable('fulfillment_settings')) {
            Schema::create('fulfillment_settings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('cafe_id')->default(1);
                $table->string('key', 50); // courier, takeaway, dine_in
                $table->string('title', 100);
                $table->string('subtitle', 255)->nullable();
                $table->text('description')->nullable();
                $table->string('status', 30)->default('active'); // active, inactive, coming_soon
                $table->string('coming_soon_note', 255)->nullable();
                $table->decimal('base_fee', 12, 2)->default(0);
                $table->decimal('min_order_amount', 12, 2)->default(0);
                $table->string('icon', 50)->nullable();
                $table->integer('sort_order')->default(0);
                $table->timestamps();

                $table->unique(['cafe_id', 'key']);
            });

            // Seed default fulfillment methods for cafe 1
            DB::table('fulfillment_settings')->insert([
                [
                    'cafe_id' => 1,
                    'key' => 'courier',
                    'title' => 'ارسال با پیک شیراز',
                    'subtitle' => 'تحویل درب آدرس توسط ناوگان پیک کافه',
                    'description' => 'سفارش شما پس از آماده‌سازی، بسته‌بندی شده و توسط پیک به آدرس شما در شیراز ارسال خواهد شد.',
                    'status' => 'active',
                    'coming_soon_note' => null,
                    'base_fee' => 0,
                    'min_order_amount' => 0,
                    'icon' => 'truck',
                    'sort_order' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'cafe_id' => 1,
                    'key' => 'takeaway',
                    'title' => 'بیرون‌بر (تحویل حضوری)',
                    'subtitle' => 'دریافت روی کانتر کافه بدون معطلی',
                    'description' => 'سفارش شما در کافه بسته‌بندی و آماده تحویل به شما یا پیک شخصی‌تان می‌شود.',
                    'status' => 'active',
                    'coming_soon_note' => null,
                    'base_fee' => 0,
                    'min_order_amount' => 0,
                    'icon' => 'shopping-bag',
                    'sort_order' => 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'cafe_id' => 1,
                    'key' => 'dine_in',
                    'title' => 'سرو در سالن کافه',
                    'subtitle' => 'پذیرایی در فضای اختصاصی بی کافه',
                    'description' => 'انتخاب شماره میز یا سرو پای بار کافه برای صرف قهوه و دسرهای تازه.',
                    'status' => 'active',
                    'coming_soon_note' => null,
                    'base_fee' => 0,
                    'min_order_amount' => 0,
                    'icon' => 'coffee',
                    'sort_order' => 3,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        // 2. Enhance Payment Gateways with Coming Soon status & note
        if (Schema::hasTable('payment_gateways')) {
            Schema::table('payment_gateways', function (Blueprint $table) {
                if (!Schema::hasColumn('payment_gateways', 'status')) {
                    $table->string('status', 30)->default('active')->after('title');
                }
                if (!Schema::hasColumn('payment_gateways', 'coming_soon_note')) {
                    $table->string('coming_soon_note', 255)->nullable()->after('status');
                }
            });

            // Synchronize existing is_active into status
            DB::statement("UPDATE payment_gateways SET status = CASE WHEN is_active = 1 THEN 'active' ELSE 'inactive' END WHERE status IS NULL OR status = 'active'");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('fulfillment_settings');

        if (Schema::hasTable('payment_gateways')) {
            Schema::table('payment_gateways', function (Blueprint $table) {
                if (Schema::hasColumn('payment_gateways', 'coming_soon_note')) {
                    $table->dropColumn('coming_soon_note');
                }
                if (Schema::hasColumn('payment_gateways', 'status')) {
                    $table->dropColumn('status');
                }
            });
        }
    }
};
