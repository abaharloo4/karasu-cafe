<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CafeSeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            SubscriptionPlanSeeder::class,
            CustomerTierSeeder::class,
            DeliveryZoneSeeder::class,
            PaymentGatewaySeeder::class,
        ]);
    }
}
