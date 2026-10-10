<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('delivery_orders');
        Schema::dropIfExists('drivers');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Redundant and obsolete tables are not restored.
    }
};
