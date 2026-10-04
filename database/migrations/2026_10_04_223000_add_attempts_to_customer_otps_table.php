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
        if (Schema::hasTable('customer_otps') && !Schema::hasColumn('customer_otps', 'attempts')) {
            Schema::table('customer_otps', function (Blueprint $table) {
                $table->unsignedTinyInteger('attempts')->default(0)->after('code');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('customer_otps') && Schema::hasColumn('customer_otps', 'attempts')) {
            Schema::table('customer_otps', function (Blueprint $table) {
                $table->dropColumn('attempts');
            });
        }
    }
};
