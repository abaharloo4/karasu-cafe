<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sms_settings', function (Blueprint $table) {
            $table->text('template_text_welcome')->nullable();
            $table->text('template_text_daily_usage')->nullable();
            $table->text('template_text_reminder')->nullable();
            $table->text('template_text_otp')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('sms_settings', function (Blueprint $table) {
            $table->dropColumn([
                'template_text_welcome',
                'template_text_daily_usage',
                'template_text_reminder',
                'template_text_otp',
            ]);
        });
    }
};
