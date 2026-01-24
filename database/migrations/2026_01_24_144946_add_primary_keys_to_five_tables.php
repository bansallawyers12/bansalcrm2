<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add primary key to account_client_receipts
        Schema::table('account_client_receipts', function (Blueprint $table) {
            $table->primary('id');
        });

        // Add primary key to activities_logs
        Schema::table('activities_logs', function (Blueprint $table) {
            $table->primary('id');
        });

        // Add primary key to application_activities_logs
        Schema::table('application_activities_logs', function (Blueprint $table) {
            $table->primary('id');
        });

        // Add primary key to mail_reports
        Schema::table('mail_reports', function (Blueprint $table) {
            $table->primary('id');
        });

        // Add primary key to notes
        Schema::table('notes', function (Blueprint $table) {
            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove primary key from account_client_receipts
        Schema::table('account_client_receipts', function (Blueprint $table) {
            $table->dropPrimary(['id']);
        });

        // Remove primary key from activities_logs
        Schema::table('activities_logs', function (Blueprint $table) {
            $table->dropPrimary(['id']);
        });

        // Remove primary key from application_activities_logs
        Schema::table('application_activities_logs', function (Blueprint $table) {
            $table->dropPrimary(['id']);
        });

        // Remove primary key from mail_reports
        Schema::table('mail_reports', function (Blueprint $table) {
            $table->dropPrimary(['id']);
        });

        // Remove primary key from notes
        Schema::table('notes', function (Blueprint $table) {
            $table->dropPrimary(['id']);
        });
    }
};
