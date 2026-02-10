<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Migrates Education and Migration documents to Documents tab:
     * - doc_type -> 'documents', category_id -> Education/Migration category id, is_edu_and_mig_doc_migrate -> 1 (success) or 2 (fail).
     *
     * @return void
     */
    public function up()
    {
        $educationCategory = DB::table('document_categories')
            ->where('name', 'Education')
            ->where('is_default', true)
            ->first();

        $migrationCategory = DB::table('document_categories')
            ->where('name', 'Migration')
            ->where('is_default', true)
            ->first();

        if (!$educationCategory) {
            Log::warning('Education/Migration docs migration: Education category (is_default=true) not found. Marking education docs as failed.');
            $failed = DB::table('documents')->where('doc_type', 'education')->update(['is_edu_and_mig_doc_migrate' => 2, 'updated_at' => now()]);
            if ($failed) {
                Log::info("Education documents migration: marked {$failed} rows as failed (is_edu_and_mig_doc_migrate=2).");
            }
        }
        if (!$migrationCategory) {
            Log::warning('Education/Migration docs migration: Migration category (is_default=true) not found. Marking migration docs as failed.');
            $failed = DB::table('documents')->where('doc_type', 'migration')->update(['is_edu_and_mig_doc_migrate' => 2, 'updated_at' => now()]);
            if ($failed) {
                Log::info("Migration documents migration: marked {$failed} rows as failed (is_edu_and_mig_doc_migrate=2).");
            }
        }

        if ($educationCategory) {
            $updated = DB::table('documents')
                ->where('doc_type', 'education')
                ->update([
                    'doc_type' => 'documents',
                    'category_id' => $educationCategory->id,
                    'is_edu_and_mig_doc_migrate' => 1,
                    'updated_at' => now(),
                ]);
            Log::info("Education documents migration: updated {$updated} rows to Documents tab (Education category).");
        }

        if ($migrationCategory) {
            $updated = DB::table('documents')
                ->where('doc_type', 'migration')
                ->update([
                    'doc_type' => 'documents',
                    'category_id' => $migrationCategory->id,
                    'is_edu_and_mig_doc_migrate' => 1,
                    'updated_at' => now(),
                ]);
            Log::info("Migration documents migration: updated {$updated} rows to Documents tab (Migration category).");
        }
    }

    /**
     * Reverse the migrations.
     * Restores doc_type and clears category_id and flag for rows that were migrated (is_edu_and_mig_doc_migrate = 1).
     *
     * @return void
     */
    public function down()
    {
        $educationCategory = DB::table('document_categories')
            ->where('name', 'Education')
            ->where('is_default', true)
            ->first();
        $migrationCategory = DB::table('document_categories')
            ->where('name', 'Migration')
            ->where('is_default', true)
            ->first();

        if ($educationCategory) {
            DB::table('documents')
                ->where('category_id', $educationCategory->id)
                ->where('doc_type', 'documents')
                ->where('is_edu_and_mig_doc_migrate', 1)
                ->update([
                    'doc_type' => 'education',
                    'category_id' => null,
                    'is_edu_and_mig_doc_migrate' => 0,
                    'updated_at' => now(),
                ]);
        }
        if ($migrationCategory) {
            DB::table('documents')
                ->where('category_id', $migrationCategory->id)
                ->where('doc_type', 'documents')
                ->where('is_edu_and_mig_doc_migrate', 1)
                ->update([
                    'doc_type' => 'migration',
                    'category_id' => null,
                    'is_edu_and_mig_doc_migrate' => 0,
                    'updated_at' => now(),
                ]);
        }
    }
};
