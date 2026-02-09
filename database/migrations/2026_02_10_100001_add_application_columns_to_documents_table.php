<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds columns to link documents to application_documents for migrated application docs.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->unsignedBigInteger('application_id')->nullable()->after('category_id')
                ->comment('Links to applications table when migrated from application_documents');
            $table->unsignedBigInteger('application_list_id')->nullable()->after('application_id')
                ->comment('Links to application_document_list when migrated from application_documents');
            $table->string('application_stage', 255)->nullable()->after('application_list_id')
                ->comment('Application stage (typename) when migrated from application_documents');
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->index('application_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropIndex(['application_id']);
            $table->dropColumn(['application_id', 'application_list_id', 'application_stage']);
        });
    }
};
