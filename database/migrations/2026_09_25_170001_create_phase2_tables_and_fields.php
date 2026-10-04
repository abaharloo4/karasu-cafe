<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Customer Tiers
        Schema::create('customer_tiers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->decimal('min_spend', 14, 2)->default(0);
            $table->integer('discount_percentage')->default(0);
            $table->boolean('free_delivery')->default(false);
            $table->string('badge_color', 20)->default('#6E756F');
            $table->timestamps();
        });

        // 2. Enhance Customers Table
        Schema::table('customers', function (Blueprint $table) {
            $table->date('birthdate')->nullable()->after('email');
            $table->foreignId('tier_id')->nullable()->after('birthdate')->constrained('customer_tiers')->nullOnDelete();
            $table->decimal('total_spent', 14, 2)->default(0)->after('tier_id');
        });

        // 3. Enhance Delivery Zones Table
        Schema::table('delivery_zones', function (Blueprint $table) {
            $table->decimal('min_order_free_delivery', 12, 2)->default(0)->after('delivery_price');
            $table->decimal('center_lat', 10, 7)->nullable()->after('min_order_free_delivery');
            $table->decimal('center_lng', 10, 7)->nullable()->after('center_lat');
            $table->decimal('radius_km', 6, 2)->nullable()->after('center_lng');
            $table->json('polygon_coordinates')->nullable()->after('radius_km');
        });

        // 4. Customer Addresses
        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->string('title', 100)->default('خانه');
            $table->text('address_line');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->foreignId('delivery_zone_id')->nullable()->constrained('delivery_zones')->nullOnDelete();
            $table->string('building_unit', 50)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        // 5. Payment Gateways (Settings)
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('key', 50)->unique();
            $table->string('title', 100);
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable();
            $table->timestamps();
        });

        // 6. Enhance Payments Table
        Schema::table('payments', function (Blueprint $table) {
            $table->string('authority_or_ref', 100)->nullable()->after('transaction_id');
            $table->string('card_pan_mask', 30)->nullable()->after('authority_or_ref');
            $table->string('receipt_image', 255)->nullable()->after('card_pan_mask');
            $table->string('tracking_number', 50)->nullable()->after('receipt_image');
            $table->text('admin_notes')->nullable()->after('tracking_number');
            $table->timestamp('verified_at')->nullable()->after('status');
        });

        // 7. Enhance Orders Table
        Schema::table('orders', function (Blueprint $table) {
            $table->string('order_number', 30)->nullable()->unique()->after('id');
            $table->enum('order_type', ['dine_in', 'courier', 'takeaway'])->default('dine_in')->after('type');
            $table->string('table_number', 20)->nullable()->after('order_type');
            $table->foreignId('delivery_address_id')->nullable()->after('table_number')->constrained('customer_addresses')->nullOnDelete();
            $table->string('payment_method', 30)->default('zarinpal')->after('delivery_address_id');
            $table->decimal('discount_amount', 12, 2)->default(0)->after('total_price');
            $table->decimal('delivery_fee', 12, 2)->default(0)->after('discount_amount');
            $table->decimal('final_payable', 12, 2)->default(0)->after('delivery_fee');
            $table->text('notes')->nullable()->after('final_payable');
            $table->integer('estimated_prep_time')->default(15)->after('notes');
        });

        // 8. Birthday Reward Logs
        Schema::create('birthday_reward_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->nullOnDelete();
            $table->integer('year');
            $table->timestamp('sent_at');
            $table->timestamps();

            $table->unique(['customer_id', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('birthday_reward_logs');

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['delivery_address_id']);
            $table->dropColumn([
                'order_number', 'order_type', 'table_number', 'delivery_address_id',
                'payment_method', 'discount_amount', 'delivery_fee', 'final_payable',
                'notes', 'estimated_prep_time'
            ]);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'authority_or_ref', 'card_pan_mask', 'receipt_image', 'tracking_number',
                'admin_notes', 'verified_at'
            ]);
        });

        Schema::dropIfExists('payment_gateways');
        Schema::dropIfExists('customer_addresses');

        Schema::table('delivery_zones', function (Blueprint $table) {
            $table->dropColumn([
                'min_order_free_delivery', 'center_lat', 'center_lng', 'radius_km', 'polygon_coordinates'
            ]);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['tier_id']);
            $table->dropColumn(['birthdate', 'tier_id', 'total_spent']);
        });

        Schema::dropIfExists('customer_tiers');
    }
};
