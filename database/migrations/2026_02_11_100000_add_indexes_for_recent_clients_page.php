<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds indexes to speed up the adminconsole recent-clients page query
     * (latest activity per client + document count/storage subqueries).
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activities_logs', function (Blueprint $table) {
            $table->index(['client_id', 'created_at']);
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->index(['client_id', 'archived_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('activities_logs', function (Blueprint $table) {
            $table->dropIndex(['client_id', 'created_at']);
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->dropIndex(['client_id', 'archived_at']);
        });
    }
};
