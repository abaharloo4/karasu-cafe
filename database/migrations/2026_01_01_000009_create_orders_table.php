<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cafe_id')->constrained('cafes')->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->enum('type', ['subscription', 'normal'])->default('normal');
            $table->enum('status', ['pending', 'processing', 'completed', 'cancelled'])->default('completed');
            $table->decimal('total_price', 12, 2)->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
