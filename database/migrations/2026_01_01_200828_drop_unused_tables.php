<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Drops unused tables that are no longer needed in the system.
     * 
     * Tables being dropped:
     * - academic_requirements (academic requirements for products - feature removed)
     * - personal_access_tokens (Laravel Sanctum tokens - not actively used, no protected API routes)
     * - fee_option_types (no data added since 2022, feature replaced by application_fee_option_types)
     * - fee_options (no data added since 2022, feature replaced by application_fee_options)
     * - online_forms (last record: 2024-08-12, feature not actively used)
     * - password_resets (Laravel default password reset table - not actively used)
     * - password_reset_links (custom password reset table - last record: 2020-08-14)
     * - product_area_levels (last record: 2022-07-14, feature not actively used)
     * 
     * To add more tables to drop, simply add them to the $tablesToDrop array below.
     *
     * @return void
     */
    public function up(): void
    {
        $tablesToDrop = [
            'academic_requirements',
            'personal_access_tokens',
            'fee_option_types',      // No data since 2022
            'fee_options',           // No data since 2022
            'online_forms',          // Last record: 2024-08-12
            'password_resets',       // Laravel default - not actively used
            'password_reset_links',  // Last record: 2020-08-14
            'product_area_levels',   // Last record: 2022-07-14
            // Add more tables here as needed
        ];

        // Drop from MySQL (if connection available)
        foreach ($tablesToDrop as $table) {
            try {
                Schema::connection('mysql')->dropIfExists($table);
            } catch (\Exception $e) {
                // MySQL connection not available, skip
            }
        }

        // Drop from PostgreSQL (if connection available)
        foreach ($tablesToDrop as $table) {
            try {
                Schema::connection('pgsql')->dropIfExists($table);
            } catch (\Exception $e) {
                // PostgreSQL connection not available, skip
            }
        }

        // Also try default connection
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
     *
     * @return void
     */
    public function down(): void
    {
        // Note: Cannot reverse table drops without schema definitions
        // These were unused tables that should not be recreated
        // If restoration is needed, use a database backup
    }
};
