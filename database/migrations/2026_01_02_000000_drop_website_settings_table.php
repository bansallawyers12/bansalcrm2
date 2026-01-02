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
     * 
     * @return void
     */
    public function up(): void
    {
        // Drop from PostgreSQL (if connection available)
        try {
            Schema::connection('pgsql')->dropIfExists('website_settings');
        } catch (\Exception $e) {
            // PostgreSQL connection not available, skip
        }

        // Drop from MySQL (if connection available)
        try {
            Schema::connection('mysql')->dropIfExists('website_settings');
        } catch (\Exception $e) {
            // MySQL connection not available, skip
        }

        // Also try default connection
        Schema::dropIfExists('website_settings');
        
        echo "Dropped table: website_settings\n";
    }

    /**
     * Reverse the migrations.
     *
     * Note: Cannot reverse table drop without schema definition.
     * If restoration is needed, use a database backup.
     *
     * @return void
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

