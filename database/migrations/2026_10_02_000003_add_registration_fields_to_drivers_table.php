<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->string('national_id', 20)->nullable()->after('phone');
            $table->string('vehicle_type', 50)->default('motorcycle')->after('national_id'); // motorcycle, car, bicycle
            $table->string('plate_number', 50)->nullable()->after('vehicle_type');
            if (!Schema::hasColumn('drivers', 'remember_token')) {
                $table->rememberToken();
            }
        });

        if (\Illuminate\Support\Facades\DB::getDriverName() === 'mysql') {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE `drivers` MODIFY COLUMN `status` ENUM('active', 'inactive', 'pending_approval', 'rejected') NOT NULL DEFAULT 'pending_approval'");
        }
    }

    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropColumn(['national_id', 'vehicle_type', 'plate_number']);
        });

        if (\Illuminate\Support\Facades\DB::getDriverName() === 'mysql') {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE `drivers` MODIFY COLUMN `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active'");
        }
    }
};
