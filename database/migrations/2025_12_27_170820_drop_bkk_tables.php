<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop all tables with 'bkk' in their names
        $tables = [
            'applications_bkk_19dec2024',
            'appointments_bkk_16apr2024',
            'book_services_bkk_9oct2024',
            'checkin_logs_bkk_13mar2024',
            'emails_bkk_2apr2025',
            'enquiries_bkk_22oct2024',
            'notes_bkk_13feb2024',
            'our_services_bkk_13may2024',
            'partner_student_invoices_bkk_30nov2024',
            'partners_bkk_18nov2024',
            'upload_checklists_bkk_29mar2025',
            'user_roles_bkk_25jan2024',
            'why_chooseuses_bkk_13may2024',
            'workflow_stages_bkk_7dec2024',
        ];

        foreach ($tables as $table) {
            // Drop from MySQL
            Schema::connection('mysql')->dropIfExists($table);
            // Drop from PostgreSQL
            Schema::connection('pgsql')->dropIfExists($table);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: Cannot reverse table drops without schema definitions
        // These were backup tables that should not be recreated
    }
};
