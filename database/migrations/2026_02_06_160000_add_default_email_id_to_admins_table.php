<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDefaultEmailIdToAdminsTable extends Migration
{
    /**
     * Run the migrations.
     * Adds default email (from Admin Console > Email tab) for sending as this user.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('admins', 'default_email_id')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->unsignedBigInteger('default_email_id')->nullable()->after('email_signature');
            });
            Schema::table('admins', function (Blueprint $table) {
                $table->foreign('default_email_id')->references('id')->on('emails')->onDelete('set null');
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
        if (Schema::hasColumn('admins', 'default_email_id')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->dropForeign(['default_email_id']);
                $table->dropColumn('default_email_id');
            });
        }
    }
}
