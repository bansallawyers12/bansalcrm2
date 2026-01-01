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
     * - invoice_followups (model exists but no usage)
     * - items (references are JavaScript arrays, not table queries)
     * - representing_partners (only 1 delete action reference, table empty)
     * - templates (system uses email_templates and crm_email_templates instead)
     * - users (system uses admins table for authentication)
     *
     * @return void
     */
    public function up(): void
    {
        // Empty unused tables identified as safe to remove
        $emptyTables = [
            'application_notes',
            'attach_files',
            'attachments',
            'invoice_followups',
            'items',
            'representing_partners',
            'templates',
            'users',
        ];

        // Drop from MySQL (if connection available)
        foreach ($emptyTables as $table) {
            try {
                Schema::connection('mysql')->dropIfExists($table);
            } catch (\Exception $e) {
                // MySQL connection not available, skip
            }
        }

        // Drop from PostgreSQL (if connection available)
        foreach ($emptyTables as $table) {
            try {
                Schema::connection('pgsql')->dropIfExists($table);
            } catch (\Exception $e) {
                // PostgreSQL connection not available, skip
            }
        }

        // Also try default connection
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
     *
     * @return void
     */
    public function down(): void
    {
        // Note: Cannot reverse table drops without schema definitions
        // These were empty unused tables that should not be recreated
        // If restoration is needed, use a database backup
    }
};

