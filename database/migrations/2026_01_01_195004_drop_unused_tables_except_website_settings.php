<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Drops unused tables as requested, keeping website_settings table.
     *
     * Tables being dropped:
     * - attachments (model exists but minimal usage)
     * - attach_files (model exists but minimal usage)
     * - check_applications (staging table for Excel imports)
     * - check_products (staging table for Excel imports)
     * - items (reusable invoice items - can be removed if not needed)
     * - representing_partners (agent-partner relationships - verify if needed)
     * - templates (legacy/unused - system uses email_templates instead)
     * - template_infos (quotation template information)
     * - users (client/customer table - WARNING: verify if clients are stored here)
     */
    public function up(): void
    {
        $tablesToDrop = [
            'attachments',
            'attach_files',
            'check_applications',
            'check_products',
            'items',
            'representing_partners',
            'templates',
            'template_infos',
            'users',
        ];

        foreach ($tablesToDrop as $table) {
            Schema::dropIfExists($table);
        }
    }

    /**
     * Reverse the migrations.
     *
     * Note: Cannot reverse table drops without schema definitions.
     * These tables should not be recreated unless needed.
     * If restoration is needed, use a database backup.
     */
    public function down(): void
    {
        // Note: Cannot reverse table drops without schema definitions
        // These were unused tables that should not be recreated
        // If restoration is needed, use a database backup
    }
};
