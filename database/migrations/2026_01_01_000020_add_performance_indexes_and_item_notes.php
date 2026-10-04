<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add notes to order_items if not exists
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'notes')) {
                $table->string('notes', 500)->nullable()->after('price');
            }
        });

        // Add composite indexes for high-frequency queries
        Schema::table('orders', function (Blueprint $table) {
            $table->index(['cafe_id', 'status', 'created_at'], 'orders_cafe_status_created_idx');
            $table->index(['customer_id', 'created_at'], 'orders_customer_created_idx');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->index(['customer_id', 'status', 'expire_date'], 'subscriptions_customer_status_expire_idx');
        });

        Schema::table('subscription_usages', function (Blueprint $table) {
            $table->index(['subscription_id', 'used_date'], 'sub_usages_sub_date_idx');
        });

        // Add rememberToken for customer web authentication
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'remember_token')) {
                $table->rememberToken();
            }
        });
    }

    public function down(): void
    {
        Schema::table('subscription_usages', function (Blueprint $table) {
            $table->dropIndex('sub_usages_sub_date_idx');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropIndex('subscriptions_customer_status_expire_idx');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_cafe_status_created_idx');
            $table->dropIndex('orders_customer_created_idx');
        });

        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'notes')) {
                $table->dropColumn('notes');
            }
        });
    }
};
