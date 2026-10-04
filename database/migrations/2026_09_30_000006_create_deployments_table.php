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
        Schema::create('deployments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cafe_id')->default(1)->constrained('cafes')->cascadeOnDelete();
            $table->string('commit_sha', 40)->index();
            $table->text('commit_message')->nullable();
            $table->string('commit_author')->nullable();
            $table->timestamp('started_at');
            $table->timestamp('finished_at')->nullable();
            $table->string('status', 30)->default('pending')->index(); // pending, running, success, failed, rolled_back
            $table->longText('log')->nullable();
            $table->string('backup_path')->nullable();
            $table->foreignId('triggered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('cpanel_task_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deployments');
    }
};
