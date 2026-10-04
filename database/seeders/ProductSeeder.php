<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'اسپرسو سینگل',
                'type' => 'coffee',
                'price' => 50000,
                'description' => 'عصاره‌گیری استاندارد ۳۰ میلی‌لیتر از دانه‌های تازه برشته‌شده ۱۰۰٪ عربیکا با بادی غنی، اسیدیته ملایم و کرمای طلایی مخملی.',
            ],
            [
                'name' => 'اسپرسو دابل',
                'type' => 'coffee',
                'price' => 70000,
                'description' => 'دو شات اسپرسو خالص (۶۰ میلی‌لیتر) با طعم‌یاد کاکائو تلخ و آجیلی، ایده‌آل برای شروع پرانرژی روز.',
            ],
            [
                'name' => 'آمریکانو',
                'type' => 'coffee',
                'price' => 80000,
                'description' => 'ترکیب دابل اسپرسو تخصصی همراه با آب جوش تصفیه‌شده، طعمی سبک‌تر با حفظ کامل رایحه اصیل قهوه.',
            ],
            [
                'name' => 'آیس آمریکانو',
                'type' => 'coffee',
                'price' => 85000,
                'description' => 'اسپرسو دوبل عصاره‌گیری‌شده روی تکه‌های یخ شفاف و آب سرد، بسیار گوارا، خنک و تازه در روزهای گرم شیراز.',
            ],
            [
                'name' => 'لته',
                'type' => 'coffee',
                'price' => 95000,
                'description' => 'ترکیب اسپرسو تک‌خاستگاه با شیر بخاردیده تازه و فوم نازک ابریشمی به همراه لته‌آرت حرفه‌ای باریستا.',
            ],
            [
                'name' => 'کاپوچینو',
                'type' => 'coffee',
                'price' => 90000,
                'description' => 'تعادل کلاسیک یک سوم اسپرسو، یک سوم شیر گرم و یک سوم میکروفوم غلیظ و خامه‌ای به همراه پودر دارچین یا کاکائو اختیاری.',
            ],
        ];

        foreach ($products as $item) {
            Product::updateOrCreate(
                ['cafe_id' => 1, 'name' => $item['name']],
                [
                    'type' => $item['type'],
                    'price' => $item['price'],
                    'description' => $item['description'],
                    'status' => 'active',
                ]
            );
        }
    }
}
