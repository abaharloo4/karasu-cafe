<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReceiptSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'cafe_id',
        'paper_width',
        'font_family',
        'font_size',
        'header_title',
        'header_subtitle',
        'invoice_title',
        'show_logo',
        'logo_url',
        'show_order_number',
        'show_date_time',
        'show_cashier_name',
        'show_customer_name',
        'show_customer_phone',
        'show_customer_club_info',
        'show_order_type',
        'show_delivery_address',
        'show_item_row_number',
        'show_item_unit_price',
        'show_item_notes',
        'show_subtotal',
        'show_discount',
        'show_delivery_fee',
        'show_tax',
        'tax_percent',
        'show_amount_in_words',
        'show_payment_method',
        'show_payment_status',
        'show_barcode',
        'show_qr_code',
        'qr_code_type',
        'custom_qr_url',
        'footer_message',
        'show_wifi',
        'wifi_ssid',
        'wifi_password',
        'show_instagram',
        'instagram_handle',
        'auto_print',
    ];

    protected $casts = [
        'show_logo' => 'boolean',
        'show_order_number' => 'boolean',
        'show_date_time' => 'boolean',
        'show_cashier_name' => 'boolean',
        'show_customer_name' => 'boolean',
        'show_customer_phone' => 'boolean',
        'show_customer_club_info' => 'boolean',
        'show_order_type' => 'boolean',
        'show_delivery_address' => 'boolean',
        'show_item_row_number' => 'boolean',
        'show_item_unit_price' => 'boolean',
        'show_item_notes' => 'boolean',
        'show_subtotal' => 'boolean',
        'show_discount' => 'boolean',
        'show_delivery_fee' => 'boolean',
        'show_tax' => 'boolean',
        'tax_percent' => 'decimal:2',
        'show_amount_in_words' => 'boolean',
        'show_payment_method' => 'boolean',
        'show_payment_status' => 'boolean',
        'show_barcode' => 'boolean',
        'show_qr_code' => 'boolean',
        'show_wifi' => 'boolean',
        'show_instagram' => 'boolean',
        'auto_print' => 'boolean',
    ];

    public function cafe(): BelongsTo
    {
        return $this->belongsTo(Cafe::class);
    }

    protected static function booted(): void
    {
        static::saved(fn ($setting) => \Illuminate\Support\Facades\Cache::forget("settings_receipt_{$setting->cafe_id}"));
        static::deleted(fn ($setting) => \Illuminate\Support\Facades\Cache::forget("settings_receipt_{$setting->cafe_id}"));
    }

    /**
     * Retrieve or create default settings for the given cafe.
     */
    public static function getForCafe(int $cafeId = 1): self
    {
        \Illuminate\Support\Facades\Cache::forget("settings_receipt_{$cafeId}");

        return static::firstOrCreate(
            ['cafe_id' => $cafeId],
            [
                'paper_width' => '80mm',
                'font_family' => 'vazirmatn',
                'font_size' => 'medium',
                'header_title' => 'بی کافه (B CAFE)',
                'header_subtitle' => 'شیراز • شعبه مرکزی تخصصی قهوه موج سوم',
                'invoice_title' => 'صورت‌حساب فروش',
                'show_logo' => true,
                'logo_url' => '/images/logo.jpeg',
                'show_order_number' => true,
                'show_date_time' => true,
                'show_cashier_name' => true,
                'show_customer_name' => true,
                'show_customer_phone' => true,
                'show_customer_club_info' => true,
                'show_order_type' => true,
                'show_delivery_address' => true,
                'show_item_row_number' => true,
                'show_item_unit_price' => true,
                'show_item_notes' => true,
                'show_subtotal' => true,
                'show_discount' => true,
                'show_delivery_fee' => true,
                'show_tax' => false,
                'tax_percent' => 0.00,
                'show_amount_in_words' => true,
                'show_payment_method' => true,
                'show_payment_status' => true,
                'show_barcode' => true,
                'show_qr_code' => true,
                'qr_code_type' => 'order_url',
                'custom_qr_url' => null,
                'footer_message' => "از حسن انتخاب و همراهی شما سپاسگزاریم\nشیراز، میدان ارم، ابتدای بلوار دانشجو\nتلفن سفارشات و هماهنگی: ۰۷۱-۳۶۲۸۰۰۰۰",
                'show_wifi' => true,
                'wifi_ssid' => 'B-Cafe-Guest',
                'wifi_password' => 'bcafe2026',
                'show_instagram' => true,
                'instagram_handle' => '@bcafe.shiraz',
                'auto_print' => true,
            ]
        );
    }

    /**
     * Get CSS font family string based on setting.
     */
    public function getFontFamilyCssAttribute(): string
    {
        return match ($this->font_family) {
            'post' => "'PostFont', 'Vazirmatn', sans-serif",
            'shabnam' => "'Shabnam', 'Vazirmatn', Tahoma, sans-serif",
            'iransans' => "'IRANSans', 'Vazirmatn', Tahoma, sans-serif",
            'b_titr' => "'B Titr', 'Vazirmatn', sans-serif",
            'courier' => "'Courier New', Courier, monospace",
            default => "'Vazirmatn', Tahoma, Arial, sans-serif",
        };
    }

    /**
     * Get base font-size in pixels based on selected size.
     */
    public function getBaseFontSizePxAttribute(): int
    {
        return match ($this->font_size) {
            'compact' => 10,
            'large' => 12,
            default => 11, // medium
        };
    }
}
