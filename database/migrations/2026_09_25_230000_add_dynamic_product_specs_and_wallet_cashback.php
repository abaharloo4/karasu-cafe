<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add dynamic product specs to products table
        Schema::table('products', function (Blueprint $table) {
            $table->integer('prep_time')->default(15)->after('description');
            $table->string('tasting_notes', 255)->nullable()->after('prep_time');
            $table->string('bean_type', 255)->nullable()->after('tasting_notes');
            $table->string('serving_type', 255)->nullable()->after('bean_type');
        });

        // 2. Add wallet_balance to customers table
        Schema::table('customers', function (Blueprint $table) {
            $table->decimal('wallet_balance', 14, 2)->default(0)->after('total_spent');
        });

        // 3. Create customer_cashbacks table for tracking cashback credits & debits
        Schema::create('customer_cashbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->enum('type', ['credit', 'debit'])->default('credit');
            $table->decimal('amount', 14, 2);
            $table->string('description', 255);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_cashbacks');

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('wallet_balance');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['prep_time', 'tasting_notes', 'bean_type', 'serving_type']);
        });
    }
};
