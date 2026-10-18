<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class SiteContentSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'cafe_id',
        'menu_image_aspect',
        'menu_title',
        'menu_subtitle',
        'about_title',
        'about_subtitle',
        'about_story',
        'about_address',
        'about_phone',
        'about_instagram',
        'about_map_lat',
        'about_map_lng',
        'about_map_neshan_url',
        'about_map_balad_url',
        'about_map_google_url',
        'subscriptions_title',
        'subscriptions_subtitle',
        'subscriptions_description',
        'subscriptions_rules',
        'footer_text',
        'footer_hours',
    ];

    public function cafe(): BelongsTo
    {
        return $this->belongsTo(Cafe::class);
    }

    protected static function booted(): void
    {
        static::saved(fn ($setting) => Cache::forget("site_content_settings_{$setting->cafe_id}"));
        static::deleted(fn ($setting) => Cache::forget("site_content_settings_{$setting->cafe_id}"));
    }

    /**
     * Retrieve or create default site content settings for the given cafe.
     */
    public static function getForCafe(int $cafeId = 1): self
    {
        $cacheKey = "site_content_settings_{$cafeId}";
        $cachedAttributes = Cache::get($cacheKey);

        if (is_array($cachedAttributes)) {
            $instance = new static();
            $instance->setRawAttributes($cachedAttributes, true);
            $instance->exists = true;
            return $instance;
        }

        // If corrupted or incomplete object is in cache, forget it immediately
        if ($cachedAttributes !== null) {
            Cache::forget($cacheKey);
        }

        $setting = static::firstOrCreate(
            ['cafe_id' => $cafeId],
            [
                'menu_image_aspect' => 'standard',
                'menu_title' => 'منوی تخصصی بی کافه',
                'menu_subtitle' => 'انتخاب از برترین دانه‌های قهوه تخصصی و دسرهای دست‌ساز در شیراز',
                'about_title' => 'داستان بی کافه؛ دانه باکیفیت و روست تازه',
                'about_subtitle' => 'تخصصی‌ترین روستری و بار قهوه شیراز، با تمرکز بر کیفیت، تازگی و پایداری',
                'about_story' => "بی کافه از سال ۱۳۹۸ با یک رویا آغاز شد: ارائه قهوه تخصصی با استانداردهای جهانی انجمن قهوه تخصصی (SCA) به عاشقان واقعی قهوه در شیراز.\n\nما معتقدیم هر فنجان قهوه داستانی از مزرعه، خاک، ارتفاع و زحمت کشاورزان را بازگو می‌کند. به همین دلیل تمامی دانه‌ها به صورت هفتگی در کارگاه اختصاصی بی کافه برشته‌کاری می‌شوند تا عطر و طعم اصیل خود را در لحظه دم‌آوری به شما هدیه دهند.",
                'about_address' => 'شیراز، میدان ارم، ابتدای بلوار دانشجو، کافه بی',
                'about_phone' => '۰۷۱-۳۶۲۸۰۰۰۰',
                'about_instagram' => '@bcafe.shiraz',
                'about_map_lat' => '29.6385',
                'about_map_lng' => '52.5186',
                'about_map_neshan_url' => 'https://nshn.ir',
                'about_map_balad_url' => 'https://balad.ir',
                'about_map_google_url' => 'https://maps.google.com',
                'subscriptions_title' => 'اشتراک ماهانه قهوه بی کافه',
                'subscriptions_subtitle' => 'صرفه‌جویی هوشمندانه با پکیج‌های روزانه اسپرسو و قهوه دمی',
                'subscriptions_description' => 'با اشتراک‌های ماهانه بی کافه، هر روز قهوه مورد علاقه خود را با تخفیف ویژه و بدون معطلی در پرداخت، به صورت حضوری یا تحویل اکسپرس دریافت کنید.',
                'subscriptions_rules' => "• هر اشتراک شامل سهمیه روزانه مشخص است.\n• در هر روز تقویمی می‌توانید ۱ فنجان از سهمیه خود را مصرف فرمایید.\n• اشتراک‌ها دارای مدت اعتبار ۳۰ روز از تاریخ فعال‌سازی می‌باشند.",
                'footer_text' => 'تمامی حقوق مادی و معنوی این وب‌سایت محفوظ و متعلق به کافه تخصصی بی شیراز می‌باشد.',
                'footer_hours' => 'همه‌روزه از ساعت ۰۸:۰۰ صبح الی ۲۳:۳۰ شب',
            ]
        );

        Cache::forever($cacheKey, $setting->getAttributes());

        return $setting;
    }

    /**
     * Get Tailwind CSS aspect-ratio class.
     */
    public function getAspectRatioClassAttribute(): string
    {
        return match ($this->menu_image_aspect) {
            'square' => 'aspect-square',
            'portrait' => 'aspect-[3/4]',
            'video' => 'aspect-video',
            default => 'aspect-[4/3]',
        };
    }
}
