<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEmailSignatureToAdminsTable extends Migration
{
    /**
     * Run the migrations.
     * Adds per-user email signature for use in checklist/reminder emails etc.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('admins', 'email_signature')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->text('email_signature')->nullable()->after('phone');
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
        if (Schema::hasColumn('admins', 'email_signature')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->dropColumn('email_signature');
            });
        }
    }
}
