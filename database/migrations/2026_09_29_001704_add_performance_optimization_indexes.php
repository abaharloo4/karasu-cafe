<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->index(['cafe_id', 'status', 'category_id'], 'products_cafe_status_cat_idx');
        });

        Schema::table('customer_otps', function (Blueprint $table) {
            $table->index(['phone', 'code', 'used_at'], 'customer_otps_phone_code_used_idx');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index(['cafe_id', 'type', 'created_at'], 'orders_cafe_type_created_idx');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_cafe_status_cat_idx');
        });

        Schema::table('customer_otps', function (Blueprint $table) {
            $table->dropIndex('customer_otps_phone_code_used_idx');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_cafe_type_created_idx');
        });
    }
};
