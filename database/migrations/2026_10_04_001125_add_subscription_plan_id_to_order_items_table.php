<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->nullable()->change();
            $table->foreignId('subscription_plan_id')->nullable()->after('product_id')->constrained('subscription_plans')->nullOnDelete();
            $table->string('item_type', 30)->default('product')->after('subscription_plan_id'); // 'product' or 'subscription'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['subscription_plan_id']);
            $table->dropColumn(['subscription_plan_id', 'item_type']);
            $table->unsignedBigInteger('product_id')->nullable(false)->change();
        });
    }
};
