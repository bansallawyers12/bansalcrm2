<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Drops company-related columns from admins table.
     * CRM company info now comes from profiles table (Profile ID 1 = Bansal Education Group).
     * Invoices use profile selection per invoice.
     *
     * @return void
     */
    public function up(): void
    {
        if (Schema::hasTable('admins')) {
            Schema::table('admins', function (Blueprint $table) {
                if (Schema::hasColumn('admins', 'company_name')) {
                    $table->dropColumn('company_name');
                }
                if (Schema::hasColumn('admins', 'company_fax')) {
                    $table->dropColumn('company_fax');
                }
                if (Schema::hasColumn('admins', 'company_website')) {
                    $table->dropColumn('company_website');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        if (Schema::hasTable('admins')) {
            Schema::table('admins', function (Blueprint $table) {
                if (!Schema::hasColumn('admins', 'company_name')) {
                    $table->string('company_name')->nullable()->after('zip');
                }
                if (!Schema::hasColumn('admins', 'company_fax')) {
                    $table->string('company_fax')->nullable()->after('company_name');
                }
                if (!Schema::hasColumn('admins', 'company_website')) {
                    $table->string('company_website')->nullable()->after('company_fax');
                }
            });
        }
    }
};
