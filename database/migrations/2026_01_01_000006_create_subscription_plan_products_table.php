<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_plan_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_plan_id')->constrained('subscription_plans')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['subscription_plan_id', 'product_id'], 'plan_product_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plan_products');
    }
};
