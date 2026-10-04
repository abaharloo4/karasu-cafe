<?php

namespace Database\Seeders;

use App\Models\DeliveryZone;
use Illuminate\Database\Seeder;

class DeliveryZoneSeeder extends Seeder
{
    public function run(): void
    {
        $zones = [
            [
                'cafe_id' => 1,
                'name' => 'منطقه ۱ - قصردشت، ارم، ستارخان و زرگری',
                'description' => 'محدوده مرکزی و غربی نزدیک کافه',
                'delivery_price' => 25000,
                'min_order_free_delivery' => 350000,
                'center_lat' => 29.6385,
                'center_lng' => 52.5050,
                'radius_km' => 5.0,
                'status' => 'active',
            ],
            [
                'cafe_id' => 1,
                'name' => 'منطقه ۲ - معالی‌آباد، فرهنگ‌شهر و تاچارا',
                'description' => 'شمال‌غربی شیراز',
                'delivery_price' => 30000,
                'min_order_free_delivery' => 400000,
                'center_lat' => 29.6750,
                'center_lng' => 52.4800,
                'radius_km' => 7.0,
                'status' => 'active',
            ],
            [
                'cafe_id' => 1,
                'name' => 'منطقه ۳ - عفیف‌آباد، ملاصدرا و خلدبرین',
                'description' => 'مرکز شیراز',
                'delivery_price' => 25000,
                'min_order_free_delivery' => 350000,
                'center_lat' => 29.6200,
                'center_lng' => 52.5200,
                'radius_km' => 4.5,
                'status' => 'active',
            ],
            [
                'cafe_id' => 1,
                'name' => 'منطقه ۴ - سایر مناطق شهری شیراز',
                'description' => 'سایر محله‌های تحت پوشش در محدوده شهری شیراز',
                'delivery_price' => 40000,
                'min_order_free_delivery' => 500000,
                'center_lat' => 29.5926,
                'center_lng' => 52.5836,
                'radius_km' => 15.0,
                'status' => 'active',
            ],
        ];

        foreach ($zones as $zone) {
            DeliveryZone::updateOrCreate(
                ['cafe_id' => $zone['cafe_id'], 'name' => $zone['name']],
                $zone
            );
        }
    }
}
