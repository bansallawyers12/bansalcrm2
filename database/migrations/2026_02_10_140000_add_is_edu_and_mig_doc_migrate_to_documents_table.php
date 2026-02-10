<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds is_edu_and_mig_doc_migrate: 0=default, 1=migrated successfully, 2=migrated failed.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->unsignedTinyInteger('is_edu_and_mig_doc_migrate')->default(0)->after('category_id')->comment('0=default, 1=edu/mig migrated successfully, 2=migrated failed');
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
            $table->dropColumn('is_edu_and_mig_doc_migrate');
        });
    }
};
