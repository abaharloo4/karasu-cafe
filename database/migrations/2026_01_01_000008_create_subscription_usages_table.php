<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cafe_id')->constrained('cafes')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('subscription_id')->constrained('subscriptions')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->date('used_date');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            // CRITICAL: Composite unique constraint to guarantee max 1 redemption per subscription per day
            $table->unique(['subscription_id', 'used_date'], 'sub_usage_daily_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_usages');
    }
};
