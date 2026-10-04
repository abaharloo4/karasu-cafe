<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receipt_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cafe_id')->default(1)->constrained('cafes')->cascadeOnDelete();
            
            // Format & Typography Settings
            $table->string('paper_width', 10)->default('80mm'); // '80mm', '58mm'
            $table->string('font_family', 30)->default('vazirmatn'); // 'vazirmatn', 'shabnam', 'iransans', 'b_titr', 'courier'
            $table->string('font_size', 20)->default('medium'); // 'compact', 'medium', 'large'
            
            // Header Customization
            $table->string('header_title', 150)->default('بی کافه (B CAFE)');
            $table->string('header_subtitle', 200)->default('شیراز • شعبه مرکزی تخصصی قهوه');
            $table->string('invoice_title', 100)->default('صورت‌حساب فروش');
            $table->boolean('show_logo')->default(true);
            $table->string('logo_url', 255)->nullable();
            
            // Meta Information Toggles
            $table->boolean('show_order_number')->default(true);
            $table->boolean('show_date_time')->default(true);
            $table->boolean('show_cashier_name')->default(true);
            $table->boolean('show_customer_name')->default(true);
            $table->boolean('show_customer_phone')->default(true);
            $table->boolean('show_customer_club_info')->default(true);
            $table->boolean('show_order_type')->default(true);
            $table->boolean('show_delivery_address')->default(true);
            
            // Items Table Toggles
            $table->boolean('show_item_row_number')->default(true);
            $table->boolean('show_item_unit_price')->default(true);
            $table->boolean('show_item_notes')->default(true);
            
            // Totals & Financials Toggles
            $table->boolean('show_subtotal')->default(true);
            $table->boolean('show_discount')->default(true);
            $table->boolean('show_delivery_fee')->default(true);
            $table->boolean('show_tax')->default(false);
            $table->decimal('tax_percent', 5, 2)->default(0.00);
            $table->boolean('show_amount_in_words')->default(true);
            
            // Payment & Tracking
            $table->boolean('show_payment_method')->default(true);
            $table->boolean('show_payment_status')->default(true);
            $table->boolean('show_barcode')->default(true);
            $table->boolean('show_qr_code')->default(true);
            $table->string('qr_code_type', 30)->default('order_url'); // 'order_url', 'instagram', 'website', 'custom'
            $table->string('custom_qr_url', 255)->nullable();
            
            // Footer & Cafe Extra Details
            $table->text('footer_message')->nullable();
            $table->boolean('show_wifi')->default(true);
            $table->string('wifi_ssid', 100)->nullable()->default('B-Cafe-Guest');
            $table->string('wifi_password', 100)->nullable()->default('bcafe2026');
            $table->boolean('show_instagram')->default(true);
            $table->string('instagram_handle', 100)->nullable()->default('@bcafe.shiraz');
            
            // Printing Behaviors
            $table->boolean('auto_print')->default(true);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receipt_settings');
    }
};
