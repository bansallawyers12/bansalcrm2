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
        // Drop enquiries table - feature removed from system (PostgreSQL only)
        Schema::connection('pgsql')->dropIfExists('enquiries');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: Cannot reverse table drop without schema definition
        // This table was removed as the enquiries/queries feature is no longer in use
    }
};
