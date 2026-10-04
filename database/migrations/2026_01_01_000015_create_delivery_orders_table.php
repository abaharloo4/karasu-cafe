<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->onDelete('set null');
            $table->foreignId('delivery_zone_id')->nullable()->constrained('delivery_zones')->onDelete('set null');
            $table->enum('status', ['pending', 'assigned', 'picked_up', 'on_the_way', 'delivered', 'cancelled'])->default('pending');
            $table->text('address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_orders');
    }
};
