<?php

namespace Database\Seeders;

use App\Models\CustomerTier;
use Illuminate\Database\Seeder;

class CustomerTierSeeder extends Seeder
{
    public function run(): void
    {
        $tiers = [
            [
                'id' => 1,
                'name' => 'برنزی',
                'min_spend' => 0,
                'discount_percentage' => 0,
                'free_delivery' => false,
                'badge_color' => '#8A918B',
            ],
            [
                'id' => 2,
                'name' => 'نقره‌ای',
                'min_spend' => 2000000,
                'discount_percentage' => 5,
                'free_delivery' => false,
                'badge_color' => '#A8AEA9',
            ],
            [
                'id' => 3,
                'name' => 'طلایی',
                'min_spend' => 5000000,
                'discount_percentage' => 10,
                'free_delivery' => true,
                'badge_color' => '#C8A35A',
            ],
            [
                'id' => 4,
                'name' => 'الماسی (VIP)',
                'min_spend' => 10000000,
                'discount_percentage' => 15,
                'free_delivery' => true,
                'badge_color' => '#3A5232',
            ],
        ];

        foreach ($tiers as $tier) {
            CustomerTier::updateOrCreate(['id' => $tier['id']], $tier);
        }
    }
}
