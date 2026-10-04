<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sms_settings', function (Blueprint $table) {
            $table->boolean('is_order_sms_enabled')->default(true)->after('is_daily_usage_enabled');

            // Pattern codes for order lifecycle
            $table->string('pattern_order_submitted', 50)->nullable()->after('pattern_expiry_warning');
            $table->string('pattern_order_preparing', 50)->nullable()->after('pattern_order_submitted');
            $table->string('pattern_order_ready', 50)->nullable()->after('pattern_order_preparing');
            $table->string('pattern_order_on_the_way', 50)->nullable()->after('pattern_order_ready');
            $table->string('pattern_order_delivered', 50)->nullable()->after('pattern_order_on_the_way');
            $table->string('pattern_order_cancelled', 50)->nullable()->after('pattern_order_delivered');

            // Text templates for order lifecycle
            $table->text('template_text_order_submitted')->nullable()->after('template_text_otp');
            $table->text('template_text_order_preparing')->nullable()->after('template_text_order_submitted');
            $table->text('template_text_order_ready')->nullable()->after('template_text_order_preparing');
            $table->text('template_text_order_on_the_way')->nullable()->after('template_text_order_ready');
            $table->text('template_text_order_delivered')->nullable()->after('template_text_order_on_the_way');
            $table->text('template_text_order_cancelled')->nullable()->after('template_text_order_delivered');
        });
    }

    public function down(): void
    {
        Schema::table('sms_settings', function (Blueprint $table) {
            $table->dropColumn([
                'is_order_sms_enabled',
                'pattern_order_submitted',
                'pattern_order_preparing',
                'pattern_order_ready',
                'pattern_order_on_the_way',
                'pattern_order_delivered',
                'pattern_order_cancelled',
                'template_text_order_submitted',
                'template_text_order_preparing',
                'template_text_order_ready',
                'template_text_order_on_the_way',
                'template_text_order_delivered',
                'template_text_order_cancelled',
            ]);
        });
    }
};
