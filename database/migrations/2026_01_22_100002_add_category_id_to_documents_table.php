<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable()->after('doc_type');
            $table->index('category_id');
        });

        // Assign all existing documents with doc_type = 'documents' to the "General" category
        $generalCategory = DB::table('document_categories')->where('name', 'General')->where('is_default', true)->first();
        
        if ($generalCategory) {
            DB::table('documents')
                ->where('doc_type', 'documents')
                ->whereNull('category_id')
                ->update(['category_id' => $generalCategory->id]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn('category_id');
        });
    }
};
