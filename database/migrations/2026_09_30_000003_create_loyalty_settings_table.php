<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loyalty_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cafe_id')->default(1)->constrained('cafes')->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->decimal('cashback_percent', 5, 2)->default(5.00);
            $table->decimal('min_order_amount', 12, 2)->default(0.00);
            $table->integer('points_per_order_unit')->default(10000); // 1 point per 10,000 Toman
            $table->integer('welcome_bonus_points')->default(20);
            $table->decimal('birthday_reward_amount', 12, 2)->default(50000.00);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_settings');
    }
};
