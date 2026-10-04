<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cafe_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('cafe_id')->constrained('categories')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });

        Schema::dropIfExists('categories');
    }
};
