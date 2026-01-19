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
        Schema::table('documents', function (Blueprint $table) {
            // Ownership & tracking
            if (!Schema::hasColumn('documents', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('id');
            }
            if (!Schema::hasColumn('documents', 'origin')) {
                $table->string('origin', 20)->default('ad_hoc')->after('created_by'); // ad_hoc|client
            }
            
            // Polymorphic association (nullable for ad-hoc documents)
            if (!Schema::hasColumn('documents', 'documentable_type')) {
                $table->string('documentable_type')->nullable();
            }
            if (!Schema::hasColumn('documents', 'documentable_id')) {
                $table->unsignedBigInteger('documentable_id')->nullable();
            }
            
            // Metadata for discoverability
            if (!Schema::hasColumn('documents', 'title')) {
                $table->string('title')->nullable();
            }
            if (!Schema::hasColumn('documents', 'document_type')) {
                $table->string('document_type', 50)->default('general'); // agreement|nda|general|contract
            }
            if (!Schema::hasColumn('documents', 'labels')) {
                $table->json('labels')->nullable();
            }
            if (!Schema::hasColumn('documents', 'due_at')) {
                $table->timestamp('due_at')->nullable();
            }
            if (!Schema::hasColumn('documents', 'priority')) {
                $table->string('priority', 10)->default('normal'); // low|normal|high
            }
            
            // Activity tracking
            if (!Schema::hasColumn('documents', 'primary_signer_email')) {
                $table->string('primary_signer_email')->nullable();
            }
            if (!Schema::hasColumn('documents', 'signer_count')) {
                $table->unsignedTinyInteger('signer_count')->default(1);
            }
            if (!Schema::hasColumn('documents', 'last_activity_at')) {
                $table->timestamp('last_activity_at')->nullable();
            }
            
            // Lifecycle
            if (!Schema::hasColumn('documents', 'archived_at')) {
                $table->timestamp('archived_at')->nullable();
            }
            
            // Signature tracking
            if (!Schema::hasColumn('documents', 'status')) {
                $table->string('status', 30)->default('draft'); // draft, signature_placed, sent, signed, voided
            }
            if (!Schema::hasColumn('documents', 'signature_doc_link')) {
                $table->text('signature_doc_link')->nullable();
            }
            if (!Schema::hasColumn('documents', 'signed_doc_link')) {
                $table->text('signed_doc_link')->nullable();
            }
            if (!Schema::hasColumn('documents', 'signed_hash')) {
                $table->string('signed_hash', 64)->nullable()->comment('SHA-256 hash for tamper detection');
            }
            if (!Schema::hasColumn('documents', 'hash_generated_at')) {
                $table->timestamp('hash_generated_at')->nullable();
            }
        });
        
        // Add indexes if they don't exist
        if (!Schema::hasIndex('documents', ['documentable_type', 'documentable_id'])) {
            Schema::table('documents', function (Blueprint $table) {
                $table->index(['documentable_type', 'documentable_id'], 'documents_documentable_index');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $columns = [
                'created_by',
                'origin',
                'documentable_type',
                'documentable_id',
                'title',
                'document_type',
                'labels',
                'due_at',
                'priority',
                'primary_signer_email',
                'signer_count',
                'last_activity_at',
                'archived_at',
                'status',
                'signature_doc_link',
                'signed_doc_link',
                'signed_hash',
                'hash_generated_at'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('documents', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
