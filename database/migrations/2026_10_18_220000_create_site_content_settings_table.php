<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_content_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cafe_id')->default(1)->constrained('cafes')->cascadeOnDelete();
            
            // Menu Display Settings
            $table->string('menu_image_aspect', 20)->default('standard'); // standard (4:3), square (1:1), portrait (3:4), video (16:9)
            $table->string('menu_title')->nullable()->default('منوی تخصصی بی کافه');
            $table->string('menu_subtitle')->nullable()->default('انتخاب از برترین دانه‌های قهوه تخصصی و دسرهای دست‌ساز در شیراز');

            // About Us & Story
            $table->string('about_title')->nullable()->default('داستان بی کافه؛ دانه باکیفیت و روست تازه');
            $table->string('about_subtitle')->nullable()->default('تخصصی‌ترین روستری و بار قهوه شیراز، با تمرکز بر کیفیت، تازگی و پایداری');
            $table->text('about_story')->nullable();
            $table->string('about_address')->nullable()->default('شیراز، میدان ارم، ابتدای بلوار دانشجو');
            $table->string('about_phone', 30)->nullable()->default('۰۷۱-۳۶۲۸۰۰۰۰');
            $table->string('about_instagram', 50)->nullable()->default('@bcafe.shiraz');
            $table->string('about_map_lat', 20)->nullable()->default('29.6385');
            $table->string('about_map_lng', 20)->nullable()->default('52.5186');
            $table->string('about_map_neshan_url')->nullable();
            $table->string('about_map_balad_url')->nullable();
            $table->string('about_map_google_url')->nullable();

            // Subscriptions Page Content
            $table->string('subscriptions_title')->nullable()->default('اشتراک قهوه ماهانه بی کافه');
            $table->string('subscriptions_subtitle')->nullable()->default('صرفه‌جویی هوشمندانه با پکیج‌های روزانه اسپرسو و قهوه دمی');
            $table->text('subscriptions_description')->nullable();
            $table->text('subscriptions_rules')->nullable();

            // Global Footer & Hours
            $table->string('footer_text', 500)->nullable()->default('تمامی حقوق مادی و معنوی این وب‌سایت متعلق به مجموعه بی کافه شیراز می‌باشد.');
            $table->string('footer_hours')->nullable()->default('همه‌روزه از ساعت ۰۸:۰۰ صبح الی ۲۳:۳۰ شب');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_content_settings');
    }
};
