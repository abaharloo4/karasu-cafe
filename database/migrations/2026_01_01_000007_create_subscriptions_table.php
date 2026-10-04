<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cafe_id')->constrained('cafes')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('subscription_plan_id')->constrained('subscription_plans')->onDelete('cascade');
            $table->integer('total_items');
            $table->integer('remaining_items');
            $table->date('start_date');
            $table->date('expire_date');
            $table->enum('status', ['active', 'expired', 'cancelled'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
