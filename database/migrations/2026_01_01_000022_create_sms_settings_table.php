<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cafe_id')->constrained('cafes')->onDelete('cascade');
            $table->string('driver', 50)->default('mellipayamak');
            $table->string('username', 100)->nullable();
            $table->string('password', 255)->nullable();
            $table->string('from_number', 50)->nullable();
            $table->integer('reminder_hour')->default(18); // 18:00 (6 PM)
            $table->boolean('is_reminder_enabled')->default(true);
            $table->boolean('is_welcome_enabled')->default(true);
            $table->boolean('is_daily_usage_enabled')->default(true);

            // Pattern codes in Melli Payamak
            $table->string('pattern_otp', 50)->nullable();
            $table->string('pattern_welcome', 50)->nullable();
            $table->string('pattern_daily_usage', 50)->nullable();
            $table->string('pattern_reminder', 50)->nullable();
            $table->string('pattern_expiry_warning', 50)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_settings');
    }
};
