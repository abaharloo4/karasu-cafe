<?php

namespace Database\Seeders;

use App\Models\Cafe;
use Illuminate\Database\Seeder;

class CafeSeeder extends Seeder
{
    public function run(): void
    {
        Cafe::firstOrCreate(
            ['id' => 1],
            [
                'name' => 'بی کافه (B Cafe)',
                'phone' => '09171234567',
                'address' => 'شیراز، بلوار ارم، پلاک ۱۰ - بی کافه',
            ]
        );
    }
}
