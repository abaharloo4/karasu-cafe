<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cafe_id')->default(1)->constrained('cafes')->cascadeOnDelete();
            $table->string('filename');
            $table->string('path')->unique();
            $table->string('disk', 30)->default('public');
            $table->string('alt_text')->nullable();
            $table->string('title')->nullable();
            $table->string('folder', 50)->default('media');
            $table->unsignedBigInteger('size')->default(0);
            $table->string('mime_type', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_items');
    }
};
