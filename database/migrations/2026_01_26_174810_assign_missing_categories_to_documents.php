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
     * Assign all existing documents with doc_type='documents' (or NULL/empty) 
     * and missing category_id to the default "General" category.
     */
    public function up(): void
    {
        // Get the General category ID
        $generalCategory = DB::table('document_categories')
            ->where('name', 'General')
            ->where('is_default', true)
            ->first();
        
        if ($generalCategory) {
            // Update all client documents with doc_type='documents' that have NULL category_id
            DB::table('documents')
                ->where('type', 'client')
                ->where('doc_type', 'documents')
                ->whereNull('category_id')
                ->update(['category_id' => $generalCategory->id]);
            
            // Also update documents with NULL or empty doc_type (for backwards compatibility)
            DB::table('documents')
                ->where('type', 'client')
                ->where(function ($query) {
                    $query->whereNull('doc_type')
                          ->orWhere('doc_type', '');
                })
                ->whereNull('category_id')
                ->update([
                    'category_id' => $generalCategory->id,
                    'doc_type' => 'documents' // Set doc_type to 'documents' for consistency
                ]);
            
            echo "Migration completed: Assigned missing categories to " . 
                 DB::table('documents')
                    ->where('type', 'client')
                    ->where('category_id', $generalCategory->id)
                    ->count() . " documents.\n";
        } else {
            echo "Warning: 'General' category not found. Please run the document_categories migration first.\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration doesn't need to be reversed as it only assigns missing values
        // Removing category_id would break the system
        echo "This migration cannot be reversed. Category assignments will remain.\n";
    }
};
