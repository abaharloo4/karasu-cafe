<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cafe_id')->constrained('cafes')->onDelete('cascade');
            $table->string('code', 50)->unique();
            $table->string('title', 100);
            $table->enum('discount_type', ['percent', 'fixed'])->default('percent');
            $table->decimal('discount_value', 12, 2);
            $table->integer('points_required')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
