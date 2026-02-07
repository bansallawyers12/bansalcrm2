<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Convert country_passport from sortname (IN, AU) to country name (India, Australia)
     * so it matches migrationmanager2 and import/export is compatible.
     * Uses PostgreSQL UPDATE ... FROM syntax.
     */
    public function up(): void
    {
        if (!Schema::hasTable('countries')) {
            return;
        }

        if (Schema::hasTable('admins') && Schema::hasColumn('admins', 'country_passport')) {
            DB::statement('
                UPDATE admins
                SET country_passport = c.name
                FROM countries c
                WHERE c.sortname = admins.country_passport
                  AND admins.country_passport IS NOT NULL
                  AND admins.country_passport != \'\'
            ');
        }

        if (Schema::hasTable('leads') && Schema::hasColumn('leads', 'country_passport')) {
            DB::statement('
                UPDATE leads
                SET country_passport = c.name
                FROM countries c
                WHERE c.sortname = leads.country_passport
                  AND leads.country_passport IS NOT NULL
                  AND leads.country_passport != \'\'
            ');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('countries')) {
            return;
        }

        if (Schema::hasTable('admins') && Schema::hasColumn('admins', 'country_passport')) {
            DB::statement('
                UPDATE admins
                SET country_passport = c.sortname
                FROM countries c
                WHERE c.name = admins.country_passport
                  AND admins.country_passport IS NOT NULL
                  AND admins.country_passport != \'\'
            ');
        }

        if (Schema::hasTable('leads') && Schema::hasColumn('leads', 'country_passport')) {
            DB::statement('
                UPDATE leads
                SET country_passport = c.sortname
                FROM countries c
                WHERE c.name = leads.country_passport
                  AND leads.country_passport IS NOT NULL
                  AND leads.country_passport != \'\'
            ');
        }
    }
};
