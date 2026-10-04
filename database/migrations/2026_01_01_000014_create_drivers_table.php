<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cafe_id')->constrained('cafes')->onDelete('cascade');
            $table->string('name', 100);
            $table->string('phone', 20)->unique();
            $table->enum('status', ['active', 'inactive', 'pending_approval', 'rejected'])->default('pending_approval');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
