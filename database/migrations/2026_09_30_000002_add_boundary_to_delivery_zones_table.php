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
        if (Schema::hasTable('delivery_zones') && !Schema::hasColumn('delivery_zones', 'boundary')) {
            Schema::table('delivery_zones', function (Blueprint $table) {
                // Stores GeoJSON polygon or coordinate array for Shiraz delivery zone
                $table->json('boundary')->nullable()->after('polygon_coordinates');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('delivery_zones') && Schema::hasColumn('delivery_zones', 'boundary')) {
            Schema::table('delivery_zones', function (Blueprint $table) {
                $table->dropColumn('boundary');
            });
        }
    }
};
