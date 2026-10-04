<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $defaultCategories = [
            'قهوه و نوشیدنی‌های گرم',
            'نوشیدنی‌های سرد و شیک',
            'کیک و دسر',
            'سایر موارد',
        ];

        foreach ($defaultCategories as $catName) {
            Category::firstOrCreate(
                ['cafe_id' => 1, 'name' => $catName],
                ['status' => 'active']
            );
        }
    }
}
