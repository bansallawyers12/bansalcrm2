<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Drops the website_settings table.
     * This table stored website configuration (phone, email, logo, etc.)
     * but is no longer needed as the data is not actively used in views.
     */
    public function up(): void
    {
        Schema::dropIfExists('website_settings');

        echo "Dropped table: website_settings\n";
    }

    /**
     * Reverse the migrations.
     *
     * Note: Cannot reverse table drop without schema definition.
     * If restoration is needed, use a database backup.
     */
    public function down(): void
    {
        // Note: Cannot reverse table drop without schema definition
        // If restoration is needed, use a database backup
        // The table structure was:
        // - id (primary key)
        // - phone (string)
        // - second_phone (string, nullable)
        // - second_email (string, nullable)
        // - ofc_timing (string, nullable)
        // - email (string)
        // - show_module (text, serialized)
        // - contact_detail (text, serialized)
        // - logo (string, nullable)
        // - social_share (text, serialized, nullable)
        // - created_at, updated_at (timestamps)
    }
};
