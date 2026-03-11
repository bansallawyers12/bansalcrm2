<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameEmailsTableToFromEmails extends Migration
{
    /**
     * Run the migrations.
     * Renames table emails to from_emails and updates foreign key from admins.default_email_id if present.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('emails')) {
            return;
        }

        // Drop FK from admins to emails if it exists (so rename doesn't break constraint)
        if (Schema::hasColumn('admins', 'default_email_id')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->dropForeign(['default_email_id']);
            });
        }

        Schema::rename('emails', 'from_emails');

        // Re-add FK from admins to from_emails if column exists
        if (Schema::hasColumn('admins', 'default_email_id')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->foreign('default_email_id')->references('id')->on('from_emails')->onDelete('set null');
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
        if (!Schema::hasTable('from_emails')) {
            return;
        }

        if (Schema::hasColumn('admins', 'default_email_id')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->dropForeign(['default_email_id']);
            });
        }

        Schema::rename('from_emails', 'emails');

        if (Schema::hasColumn('admins', 'default_email_id')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->foreign('default_email_id')->references('id')->on('emails')->onDelete('set null');
            });
        }
    }
}
