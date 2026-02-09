<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds is_migrated column to track application document migration to documents table.
     * 0 = not migrated (default), 1 = migrated, 2 = skipped (duplicate found)
     *
     * @return void
     */
    public function up()
    {
        Schema::table('application_documents', function (Blueprint $table) {
            $table->unsignedTinyInteger('is_migrated')->default(0)->after('typename')
                ->comment('0=not migrated, 1=migrated, 2=skipped duplicate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('application_documents', function (Blueprint $table) {
            $table->dropColumn('is_migrated');
        });
    }
};
