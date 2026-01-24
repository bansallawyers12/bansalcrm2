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
     * This migration syncs PostgreSQL sequences with actual table data for 24 tables
     * that have sequence synchronization issues. This prevents duplicate key errors
     * when inserting new records.
     * 
     * SAFETY: This migration ONLY updates sequence counters. It does NOT modify,
     * delete, or insert any data in the tables themselves.
     */
    public function up(): void
    {
        // List of all 24 tables with sequence issues (from check_sequences.php scan)
        $tables = [
            'admins',                           // 223 records behind
            'agents',                           // 1 record behind
            'application_documents',            // 10,150 records behind
            'application_fee_option_types',     // 13,292 records behind
            'application_fee_options',          // 1,103 records behind
            'applications',                     // 902 records behind
            'checkin_logs',                     // 333 records behind
            'client_phones',                    // 496 records behind
            'income_sharings',                  // 135 records behind
            'interested_services',              // 467 records behind
            'invoice_details',                  // 257 records behind
            'invoice_payments',                 // 231 records behind
            'invoices',                         // 221 records behind
            'notifications',                    // 5,076 records behind
            'partner_branches',                 // 11 records behind
            'partner_emails',                   // 28 records behind
            'partner_phones',                   // 24 records behind
            'partner_student_invoices',         // 262 records behind
            'partners',                         // 7 records behind
            'products',                         // 58 records behind
            'tags',                             // 1 record behind
            'test_scores',                      // 54 records behind
            'user_logs',                        // 386 records behind
            'verified_numbers',                 // 2 records behind
        ];

        echo "\n=== SYNCING SEQUENCES FOR 24 TABLES ===\n\n";

        foreach ($tables as $table) {
            $sequenceName = $table . '_id_seq';
            
            // Get the maximum ID from the table (READ-ONLY operation)
            $maxId = DB::table($table)->max('id');
            
            if ($maxId === null) {
                echo "⚠ Skipping {$table} (no records found)\n";
                continue;
            }
            
            // Get current sequence value (READ-ONLY operation)
            $currentSeq = DB::selectOne("SELECT last_value FROM {$sequenceName}")->last_value;
            
            // Calculate the gap
            $gap = $maxId - $currentSeq;
            
            // Sync the sequence to match max ID (ONLY updates the sequence counter)
            DB::statement("SELECT setval('{$sequenceName}', {$maxId})");
            
            // Verify the fix
            $newSeq = DB::selectOne("SELECT last_value FROM {$sequenceName}")->last_value;
            
            echo "✓ {$table}: max_id={$maxId}, old_seq={$currentSeq}, new_seq={$newSeq}, gap_fixed={$gap}\n";
        }

        echo "\n=== SEQUENCE SYNC COMPLETE ===\n";
        echo "All 24 table sequences have been synced with their data.\n";
        echo "Next insert operations will use correct IDs.\n\n";
    }

    /**
     * Reverse the migrations.
     * 
     * NOTE: Rolling back this migration is not recommended as it would
     * desynchronize sequences again. However, if needed, it will set
     * sequences back to their current max IDs.
     */
    public function down(): void
    {
        echo "\n⚠ WARNING: Rolling back sequence sync is not recommended.\n";
        echo "Sequences will remain at their current values to prevent data issues.\n\n";
        
        // Intentionally left empty - rolling back sequence syncs could cause issues
        // If rollback is needed, sequences will stay at current values
    }
};
