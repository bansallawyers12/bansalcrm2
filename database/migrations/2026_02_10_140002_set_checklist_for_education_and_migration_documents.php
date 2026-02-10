<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Sets checklist = 'Education' or 'Migration' for documents in those categories
     * where checklist is currently NULL or empty (so the Documents tab shows the correct label).
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

        if ($educationCategory) {
            $updated = DB::table('documents')
                ->where('category_id', $educationCategory->id)
                ->where(function ($query) {
                    $query->whereNull('checklist')->orWhere('checklist', '');
                })
                ->update([
                    'checklist' => 'Education',
                    'updated_at' => now(),
                ]);
            Log::info("Checklist migration: set checklist='Education' for {$updated} document(s).");
        }

        if ($migrationCategory) {
            $updated = DB::table('documents')
                ->where('category_id', $migrationCategory->id)
                ->where(function ($query) {
                    $query->whereNull('checklist')->orWhere('checklist', '');
                })
                ->update([
                    'checklist' => 'Migration',
                    'updated_at' => now(),
                ]);
            Log::info("Checklist migration: set checklist='Migration' for {$updated} document(s).");
        }
    }

    /**
     * Reverse the migrations.
     * Clears checklist back to null for rows we set to 'Education' or 'Migration'.
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
                ->where('checklist', 'Education')
                ->update(['checklist' => null, 'updated_at' => now()]);
        }
        if ($migrationCategory) {
            DB::table('documents')
                ->where('category_id', $migrationCategory->id)
                ->where('checklist', 'Migration')
                ->update(['checklist' => null, 'updated_at' => now()]);
        }
    }
};
