<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsSetting extends Model
{
    use HasFactory;

    public const DEFAULT_WELCOME_TEXT = '{name} عزیز، اشتراک «{plan_name}» در بی کافه با موفقیت فعال گردید. مهلت استفاده: تا {expire_date}. به امید دیدارتان!';
    public const DEFAULT_DAILY_USAGE_TEXT = '{name} عزیز، سهمیه روزانه «{product_name}» در بی کافه تحویل داده شد. سهمیه باقیمانده شما: {remaining_items} عدد.';
    public const DEFAULT_REMINDER_TEXT = 'سلام {name} عزیز، امروز هنوز سهمیه رایگان قهوه اشتراک «{plan_name}» خود را در بی کافه دریافت نکرده‌اید! منتظر دیدارتان هستیم.';
    public const DEFAULT_OTP_TEXT = 'کد تایید ورود شما به سامانه بی کافه: {code}';
    public const DEFAULT_ORDER_SUBMITTED_TEXT = '{name} عزیز، سفارش شما با شماره {order_number} در بی کافه ثبت شد و به زودی آماده‌سازی می‌شود.';
    public const DEFAULT_ORDER_PREPARING_TEXT = '{name} عزیز، سفارش {order_number} توسط باریستای بی کافه در حال آماده‌سازی است.';
    public const DEFAULT_ORDER_READY_TEXT = '{name} عزیز، سفارش {order_number} آماده است! می‌توانید آن را تحویل بگیرید.';
    public const DEFAULT_ORDER_ON_THE_WAY_TEXT = '{name} عزیز، سفارش {order_number} به پیک تحویل داده شد و در مسیر ارسال به آدرس شماست.';
    public const DEFAULT_ORDER_DELIVERED_TEXT = '{name} عزیز، سفارش {order_number} تحویل داده شد. نوش جان! از اینکه بی کافه را انتخاب کردید سپاسگزاریم.';
    public const DEFAULT_ORDER_CANCELLED_TEXT = '{name} عزیز، سفارش {order_number} شما در بی کافه لغو شد.';

    protected $fillable = [
        'cafe_id',
        'driver',
        'username',
        'password',
        'from_number',
        'reminder_hour',
        'is_reminder_enabled',
        'is_welcome_enabled',
        'is_daily_usage_enabled',
        'is_order_sms_enabled',
        'pattern_otp',
        'pattern_welcome',
        'pattern_daily_usage',
        'pattern_reminder',
        'pattern_expiry_warning',
        'pattern_order_submitted',
        'pattern_order_preparing',
        'pattern_order_ready',
        'pattern_order_on_the_way',
        'pattern_order_delivered',
        'pattern_order_cancelled',
        'template_text_welcome',
        'template_text_daily_usage',
        'template_text_reminder',
        'template_text_otp',
        'template_text_order_submitted',
        'template_text_order_preparing',
        'template_text_order_ready',
        'template_text_order_on_the_way',
        'template_text_order_delivered',
        'template_text_order_cancelled',
    ];

    protected $casts = [
        'password' => 'encrypted',
        'reminder_hour' => 'integer',
        'is_reminder_enabled' => 'boolean',
        'is_welcome_enabled' => 'boolean',
        'is_daily_usage_enabled' => 'boolean',
        'is_order_sms_enabled' => 'boolean',
    ];

    public function cafe(): BelongsTo
    {
        return $this->belongsTo(Cafe::class);
    }
}
