<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Drop verify_users table. It has no references in the codebase and is orphaned.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::dropIfExists('verify_users');
    }

    /**
     * Reverse the migrations.
     * Cannot recreate - table structure unknown; restore from backup if needed.
     *
     * @return void
     */
    public function down(): void
    {
        // Table was orphaned; no schema to restore
    }
};
