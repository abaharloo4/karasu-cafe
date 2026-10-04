<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lucky_wheel_spins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('lucky_wheel_prize_id')->nullable()->constrained('lucky_wheel_prizes')->onDelete('set null');
            $table->date('spin_date');
            $table->timestamps();

            // Strict composite unique index to guarantee max 1 spin per day per customer
            $table->unique(['customer_id', 'spin_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lucky_wheel_spins');
    }
};
