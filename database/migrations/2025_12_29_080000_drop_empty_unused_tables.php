<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Drops empty unused tables that have no data and are not referenced in the codebase.
     * These tables were identified as safe to remove after codebase analysis.
     *
     * Tables being dropped:
     * - application_notes (no references found)
     * - attach_files (only path config, no active usage)
     * - attachments (references are form fields, not table queries)
     * - items (references are JavaScript arrays, not table queries)
     * - representing_partners (only 1 delete action reference, table empty)
     * - templates (system uses email_templates and crm_email_templates instead)
     * - users (system uses admins table for authentication)
     */
    public function up(): void
    {
        // Empty unused tables identified as safe to remove
        $emptyTables = [
            'application_notes',
            'attach_files',
            'attachments',
            'items',
            'representing_partners',
            'templates',
            'users',
        ];

        foreach ($emptyTables as $table) {
            Schema::dropIfExists($table);
        }
    }

    /**
     * Reverse the migrations.
     *
     * Note: Cannot reverse table drops without schema definitions.
     * These were empty unused tables that should not be recreated.
     * If needed, restore from database backup.
     */
    public function down(): void
    {
        // Note: Cannot reverse table drops without schema definitions
        // These were empty unused tables that should not be recreated
        // If restoration is needed, use a database backup
    }
};
