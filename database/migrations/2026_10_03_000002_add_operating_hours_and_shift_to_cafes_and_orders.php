<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cafes', function (Blueprint $table) {
            if (!Schema::hasColumn('cafes', 'order_start_time')) {
                $table->string('order_start_time', 10)->default('08:00')->after('address');
            }
            if (!Schema::hasColumn('cafes', 'order_end_time')) {
                $table->string('order_end_time', 10)->default('22:00')->after('order_start_time');
            }
            if (!Schema::hasColumn('cafes', 'is_ordering_open')) {
                $table->boolean('is_ordering_open')->default(true)->after('order_end_time');
            }
            if (!Schema::hasColumn('cafes', 'closed_message')) {
                $table->text('closed_message')->nullable()->after('is_ordering_open');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'is_queued_for_next_shift')) {
                $table->boolean('is_queued_for_next_shift')->default(false)->after('estimated_prep_time');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cafes', function (Blueprint $table) {
            $table->dropColumn(['order_start_time', 'order_end_time', 'is_ordering_open', 'closed_message']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['is_queued_for_next_shift']);
        });
    }
};
