<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lucky_wheel_prizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cafe_id')->constrained('cafes')->onDelete('cascade');
            $table->string('title', 100);
            $table->enum('prize_type', ['points', 'coupon', 'free_coffee', 'empty'])->default('points');
            $table->string('prize_value', 100)->default('0');
            $table->unsignedInteger('win_probability')->default(10); // Probability percentage 1-100
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lucky_wheel_prizes');
    }
};
