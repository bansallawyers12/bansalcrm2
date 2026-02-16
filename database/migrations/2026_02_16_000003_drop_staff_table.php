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
        Schema::dropIfExists('staff');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate staff table - run create_staff_table and migrate_staff_data migrations to restore
        // This down() does nothing; the table is intentionally dropped.
    }
};
