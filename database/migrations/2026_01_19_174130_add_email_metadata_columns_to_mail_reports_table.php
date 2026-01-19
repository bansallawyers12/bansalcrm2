<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('mail_reports', function (Blueprint $table) {
            // Email metadata fields
            $table->string('message_id')->nullable()->after('mail_body_type');
            $table->string('thread_id')->nullable()->after('message_id');
            $table->timestamp('received_date')->nullable()->after('thread_id');
            $table->string('file_hash', 64)->nullable()->after('received_date');
            
            // AI analysis fields
            $table->json('python_analysis')->nullable()->after('file_hash');
            $table->string('category')->nullable()->default('Uncategorized')->after('python_analysis');
            $table->string('priority')->nullable()->default('low')->after('category');
            $table->string('sentiment')->nullable()->default('neutral')->after('priority');
            $table->string('language', 10)->nullable()->after('sentiment');
            $table->json('security_issues')->nullable()->after('language');
            $table->json('thread_info')->nullable()->after('security_issues');
            $table->timestamp('processed_at')->nullable()->after('thread_info');
            
            // Index for duplicate detection
            $table->index('file_hash');
            $table->index('message_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mail_reports', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex(['file_hash']);
            $table->dropIndex(['message_id']);
            
            // Drop columns
            $table->dropColumn([
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
            ]);
        });
    }
};
