<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE `sms_settings` MODIFY `password` TEXT NULL');
        } else {
            Schema::table('sms_settings', function (Blueprint $table) {
                $table->text('password')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE `sms_settings` MODIFY `password` VARCHAR(255) NULL');
        } else {
            Schema::table('sms_settings', function (Blueprint $table) {
                $table->string('password', 255)->nullable()->change();
            });
        }
    }
};
