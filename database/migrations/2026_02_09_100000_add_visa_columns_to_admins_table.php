<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVisaColumnsToAdminsTable extends Migration
{
    /**
     * Run the migrations.
     * Adds visa_type_id, visa_country, visa_grant_date for alignment with migrationmanager2 (one visa per client).
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('admins', 'visa_type_id')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->unsignedBigInteger('visa_type_id')->nullable();
            });
            Schema::table('admins', function (Blueprint $table) {
                $table->foreign('visa_type_id')->references('id')->on('visa_types')->onDelete('set null');
            });
        }
        if (!Schema::hasColumn('admins', 'visa_country')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->string('visa_country', 255)->nullable();
            });
        }
        if (!Schema::hasColumn('admins', 'visa_grant_date')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->date('visa_grant_date')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('admins', 'visa_type_id')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->dropForeign(['visa_type_id']);
                $table->dropColumn('visa_type_id');
            });
        }
        if (Schema::hasColumn('admins', 'visa_country')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->dropColumn('visa_country');
            });
        }
        if (Schema::hasColumn('admins', 'visa_grant_date')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->dropColumn('visa_grant_date');
            });
        }
    }
}
