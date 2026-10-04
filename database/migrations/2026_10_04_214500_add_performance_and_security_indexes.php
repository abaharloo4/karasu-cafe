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
        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                if (Schema::hasColumn('payments', 'authority_or_ref')) {
                    $table->index('authority_or_ref', 'payments_authority_ref_idx');
                }
                if (Schema::hasColumn('payments', 'tracking_number')) {
                    $table->index('tracking_number', 'payments_tracking_number_idx');
                }
                if (Schema::hasColumns('payments', ['gateway', 'created_at'])) {
                    $table->index(['gateway', 'created_at'], 'payments_gateway_created_idx');
                }
            });
        }

        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (Schema::hasColumns('orders', ['cafe_id', 'created_at'])) {
                    $table->index(['cafe_id', 'created_at'], 'orders_cafe_created_idx');
                }
            });
        }

        if (Schema::hasTable('pwa_events')) {
            Schema::table('pwa_events', function (Blueprint $table) {
                if (Schema::hasColumns('pwa_events', ['panel', 'event_type', 'created_at'])) {
                    $table->index(['panel', 'event_type', 'created_at'], 'pwa_events_panel_type_created_idx');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropIndex('payments_authority_ref_idx');
                $table->dropIndex('payments_tracking_number_idx');
                $table->dropIndex('payments_gateway_created_idx');
            });
        }

        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropIndex('orders_cafe_created_idx');
            });
        }

        if (Schema::hasTable('pwa_events')) {
            Schema::table('pwa_events', function (Blueprint $table) {
                $table->dropIndex('pwa_events_panel_type_created_idx');
            });
        }
    }
};
