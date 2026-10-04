<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->index('phone', 'customers_phone_idx');
            $table->index(['cafe_id', 'name'], 'customers_cafe_name_idx');
        });

        Schema::table('loyalty_points', function (Blueprint $table) {
            $table->index(['customer_id', 'created_at'], 'loyalty_points_customer_created_idx');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex('customers_phone_idx');
            $table->dropIndex('customers_cafe_name_idx');
        });

        Schema::table('loyalty_points', function (Blueprint $table) {
            $table->dropIndex('loyalty_points_customer_created_idx');
        });
    }
};
