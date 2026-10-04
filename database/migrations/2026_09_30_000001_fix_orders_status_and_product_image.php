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
        // 1. Add image column to products table if missing
        if (Schema::hasTable('products') && !Schema::hasColumn('products', 'image')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('image', 500)->nullable()->after('serving_type');
            });
        }

        // 2. Fix orders table status & add fulfillment_type
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                // Change status to string to support all Phase 2 workflow statuses
                // (pending, awaiting_payment_verification, preparing, ready, shipping, out_for_delivery, delivered, completed, cancelled)
                $table->string('status', 50)->default('pending')->change();

                if (!Schema::hasColumn('orders', 'fulfillment_type')) {
                    $table->string('fulfillment_type', 30)->default('pickup')->after('order_type');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('products') && Schema::hasColumn('products', 'image')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('image');
            });
        }

        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'fulfillment_type')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('fulfillment_type');
            });
        }
    }
};
