<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Owner / Admin User
        User::updateOrCreate(
            ['role' => 'admin'],
            [
                'cafe_id' => 1,
                'name' => 'عباس کشاورز',
                'email' => 'admin@bcafe.ir',
                'phone' => '09938266103',
                'password' => Hash::make('12345678'),
                'role' => 'admin',
            ]
        );

        // Staff / Barista User
        User::updateOrCreate(
            ['role' => 'staff'],
            [
                'cafe_id' => 1,
                'name' => 'پرسنل صندوق',
                'email' => 'staff@bcafe.ir',
                'phone' => '09123456789',
                'password' => Hash::make('12345678'),
                'role' => 'staff',
            ]
        );
    }
}
