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
        // Drop unused/legacy tables
        $tablesToDrop = [
            'cashbacks',
            'client_monthly_rewards',
            'download_schedule_dates',
            'enquiry_sources',
            'groups',
            'markups',
            'mentorings',
            'nature_of_enquiry',
            // Laravel Passport OAuth tables (not used - system uses Sanctum instead)
            'oauth_access_tokens',
            'oauth_auth_codes',
            'oauth_clients',
            'oauth_personal_access_clients',
            'oauth_refresh_tokens',
            // Promo code tables (broken/legacy - references removed appointment system)
            'promo_codes',
            'promocode_uses',
            'wallets',
        ];

        // Drop tables from the default database connection
        foreach ($tablesToDrop as $table) {
            try {
                Schema::dropIfExists($table);
            } catch (\Exception $e) {
                // Table may not exist or connection issue, skip silently
                continue;
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: Cannot reverse table drops without schema definitions
        // These were unused/legacy tables that should not be recreated
    }
};
