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
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedTinyInteger('discount_percent')->default(0)->after('price'); // e.g. 15 for 15%
            $table->string('discount_start_time', 5)->nullable()->after('discount_percent'); // '16:00'
            $table->string('discount_end_time', 5)->nullable()->after('discount_start_time'); // '19:00'
            $table->boolean('is_hourly_discount_active')->default(false)->after('discount_end_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'discount_percent',
                'discount_start_time',
                'discount_end_time',
                'is_hourly_discount_active',
            ]);
        });
    }
};
