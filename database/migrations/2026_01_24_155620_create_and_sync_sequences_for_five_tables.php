<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration creates sequences for 5 tables and syncs them with existing data.
     * These tables had primary keys added but lacked auto-increment sequences.
     */
    public function up(): void
    {
        $tables = [
            'account_client_receipts',
            'activities_logs',
            'application_activities_logs',
            'mail_reports',
            'notes'
        ];

        foreach ($tables as $table) {
            $sequenceName = $table . '_id_seq';
            
            // Get the maximum ID from the table
            $maxId = DB::table($table)->max('id');
            $startValue = $maxId ? $maxId + 1 : 1;
            
            // Create the sequence starting from max ID + 1
            DB::statement("CREATE SEQUENCE IF NOT EXISTS {$sequenceName} START WITH {$startValue}");
            
            // Set the default value for the id column to use the sequence
            DB::statement("ALTER TABLE {$table} ALTER COLUMN id SET DEFAULT nextval('{$sequenceName}')");
            
            // Associate the sequence with the column (so it gets dropped if column is dropped)
            DB::statement("ALTER SEQUENCE {$sequenceName} OWNED BY {$table}.id");
            
            echo "✓ Created and synced sequence for {$table} (starting at {$startValue})\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'account_client_receipts',
            'activities_logs',
            'application_activities_logs',
            'mail_reports',
            'notes'
        ];

        foreach ($tables as $table) {
            $sequenceName = $table . '_id_seq';
            
            // Remove the default value from id column
            DB::statement("ALTER TABLE {$table} ALTER COLUMN id DROP DEFAULT");
            
            // Drop the sequence
            DB::statement("DROP SEQUENCE IF EXISTS {$sequenceName}");
            
            echo "✓ Removed sequence for {$table}\n";
        }
    }
};
