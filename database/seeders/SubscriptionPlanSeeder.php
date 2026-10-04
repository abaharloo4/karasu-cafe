<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plan = SubscriptionPlan::firstOrCreate(
            ['cafe_id' => 1, 'name' => 'اشتراک ۱۰۰ روزه قهوه'],
            [
                'total_items' => 100,
                'duration_days' => 100,
                'price' => 5000000,
                'status' => 'active',
            ]
        );

        // Attach allowed products (Espresso Single, Espresso Double, Americano, Iced Americano)
        $allowedProductIds = Product::whereIn('name', [
            'اسپرسو سینگل',
            'اسپرسو دابل',
            'آمریکانو',
            'آیس آمریکانو',
        ])->pluck('id');

        $plan->products()->sync($allowedProductIds);
    }
}
