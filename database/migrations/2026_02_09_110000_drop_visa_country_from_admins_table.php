<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropVisaCountryFromAdminsTable extends Migration
{
    /**
     * Run the migrations.
     * Remove visa_country column; we do not use it in bansalcrm2.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('admins', 'visa_country')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->dropColumn('visa_country');
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
        if (!Schema::hasColumn('admins', 'visa_country')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->string('visa_country', 255)->nullable();
            });
        }
    }
}
