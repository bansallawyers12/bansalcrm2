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
     * This migration safely adds email metadata columns to mail_reports table.
     * It's backward compatible and won't break existing functionality.
     */
    public function up(): void
    {
        Schema::table('mail_reports', function (Blueprint $table) {
            // Check and add columns only if they don't exist
            // This ensures backward compatibility
            
            $columns = Schema::getColumnListing('mail_reports');
            
            // Email metadata fields
            if (!in_array('message_id', $columns)) {
                $table->string('message_id')->nullable()->after('mail_body_type');
            }
            
            if (!in_array('thread_id', $columns)) {
                $table->string('thread_id')->nullable()->after('message_id');
            }
            
            if (!in_array('received_date', $columns)) {
                $table->timestamp('received_date')->nullable()->after('thread_id');
            }
            
            if (!in_array('file_hash', $columns)) {
                $table->string('file_hash', 64)->nullable()->after('received_date');
            }
            
            // AI analysis fields
            if (!in_array('python_analysis', $columns)) {
                $table->json('python_analysis')->nullable()->after('file_hash');
            }
            
            if (!in_array('category', $columns)) {
                $table->string('category')->nullable()->default('Uncategorized')->after('python_analysis');
            }
            
            if (!in_array('priority', $columns)) {
                $table->string('priority')->nullable()->default('low')->after('category');
            }
            
            if (!in_array('sentiment', $columns)) {
                $table->string('sentiment')->nullable()->default('neutral')->after('priority');
            }
            
            if (!in_array('language', $columns)) {
                $table->string('language', 10)->nullable()->after('sentiment');
            }
            
            if (!in_array('security_issues', $columns)) {
                $table->json('security_issues')->nullable()->after('language');
            }
            
            if (!in_array('thread_info', $columns)) {
                $table->json('thread_info')->nullable()->after('security_issues');
            }
            
            if (!in_array('processed_at', $columns)) {
                $table->timestamp('processed_at')->nullable()->after('thread_info');
            }
        });
        
        // Add indexes only if they don't already exist
        $this->addIndexIfNotExists('mail_reports', 'file_hash');
        $this->addIndexIfNotExists('mail_reports', 'message_id');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mail_reports', function (Blueprint $table) {
            // Drop indexes first (if they exist)
            try {
                $table->dropIndex(['file_hash']);
            } catch (\Exception $e) {
                // Index doesn't exist, continue
            }
            
            try {
                $table->dropIndex(['message_id']);
            } catch (\Exception $e) {
                // Index doesn't exist, continue
            }
            
            // Drop columns only if they exist
            $columns = Schema::getColumnListing('mail_reports');
            $columnsToDrop = [];
            
            $possibleColumns = [
                'message_id',
                'thread_id',
                'received_date',
                'file_hash',
                'python_analysis',
                'category',
                'priority',
                'sentiment',
                'language',
                'security_issues',
                'thread_info',
                'processed_at'
            ];
            
            foreach ($possibleColumns as $col) {
                if (in_array($col, $columns)) {
                    $columnsToDrop[] = $col;
                }
            }
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
    
    /**
     * Add index if it doesn't exist
     */
    private function addIndexIfNotExists(string $table, string $column): void
    {
        $indexName = $table . '_' . $column . '_index';
        
        // Check if index exists
        $indexes = DB::select("
            SELECT indexname 
            FROM pg_indexes 
            WHERE tablename = ? 
            AND indexname = ?
        ", [$table, $indexName]);
        
        if (empty($indexes)) {
            DB::statement("CREATE INDEX {$indexName} ON {$table} ({$column})");
        }
    }
};
