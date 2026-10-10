<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. WooCommerce-style coupon capabilities
        Schema::table('coupons', function (Blueprint $table) {
            if (!Schema::hasColumn('coupons', 'description')) {
                $table->string('description', 255)->nullable()->after('title');
            }
            if (!Schema::hasColumn('coupons', 'min_spend')) {
                $table->decimal('min_spend', 12, 2)->nullable()->after('discount_value');
            }
            if (!Schema::hasColumn('coupons', 'max_discount')) {
                $table->decimal('max_discount', 12, 2)->nullable()->after('min_spend');
            }
            if (!Schema::hasColumn('coupons', 'usage_limit')) {
                $table->integer('usage_limit')->nullable()->after('points_required');
            }
            if (!Schema::hasColumn('coupons', 'usage_limit_per_user')) {
                $table->integer('usage_limit_per_user')->nullable()->after('usage_limit');
            }
            if (!Schema::hasColumn('coupons', 'times_used')) {
                $table->integer('times_used')->default(0)->after('usage_limit_per_user');
            }
            if (!Schema::hasColumn('coupons', 'first_order_only')) {
                $table->boolean('first_order_only')->default(false)->after('times_used');
            }
            if (!Schema::hasColumn('coupons', 'starts_at')) {
                $table->timestamp('starts_at')->nullable()->after('expires_at');
            }
        });

        // 2. Birthday reward options in loyalty settings
        Schema::table('loyalty_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('loyalty_settings', 'is_birthday_reward_enabled')) {
                $table->boolean('is_birthday_reward_enabled')->default(true)->after('birthday_reward_amount');
            }
            if (!Schema::hasColumn('loyalty_settings', 'birthday_reward_type')) {
                $table->string('birthday_reward_type', 20)->default('wallet')->after('is_birthday_reward_enabled');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn([
                'description',
                'min_spend',
                'max_discount',
                'usage_limit',
                'usage_limit_per_user',
                'times_used',
                'first_order_only',
                'starts_at',
            ]);
        });

        Schema::table('loyalty_settings', function (Blueprint $table) {
            $table->dropColumn([
                'is_birthday_reward_enabled',
                'birthday_reward_type',
            ]);
        });
    }
};
