<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Drops multiple unused tables from the database.
     * 
     * Tables being dropped:
     * - check_partners: Staging table used during Excel imports (similar to check_applications and check_products)
     * - api_tokens: Unused API token authentication table (system does not use token-based API authentication)
     * - cities: Unused table (City model exists but is not actively used)
     * - currencies: Unused table (currency selection uses Bootstrap Form Helpers library, not database table)
     * - postcode_ranges: Unused table (no models or controllers reference this table)
     * - quotation_infos: Unused table (legacy quotation feature)
     * - quotations: Unused table (referenced in ClientsController for merging clients, but feature appears unused)
     * - states: Unused table (referenced in AdminController and Admin model, but feature appears unused)
     * - test_scores: Unused table (referenced in EducationController and views, but feature appears unused)
     *
     * @return void
     */
    public function up(): void
    {
        $tablesToDrop = [
            'check_partners',
            'api_tokens',
            'cities',
            'currencies',
            'postcode_ranges',
            'quotation_infos',
            'quotations',
            'states',
            'test_scores',
        ];

        // Drop from PostgreSQL only
        foreach ($tablesToDrop as $table) {
            try {
                Schema::connection('pgsql')->dropIfExists($table);
            } catch (\Exception $e) {
                // PostgreSQL connection not available, skip
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * Note: Cannot reverse table drops without schema definitions.
     * These tables are no longer needed and should not be recreated.
     * If restoration is needed, use a database backup.
     *
     * @return void
     */
    public function down(): void
    {
        // Note: Cannot reverse table drops without schema definitions
        // These tables are no longer needed and should not be recreated
        // If restoration is needed, use a database backup
    }
};
