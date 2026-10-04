<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cafe_id')->constrained('cafes')->onDelete('cascade');
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();

            $table->unique(['cafe_id', 'phone']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
