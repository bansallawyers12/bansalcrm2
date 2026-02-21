<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Removes practically unused columns: sat_i, sat_ii, gre, gmat (1 row each, no UI or logic usage).
     */
    public function up(): void
    {
        Schema::table('test_scores', function (Blueprint $table) {
            $table->dropColumn(['sat_i', 'sat_ii', 'gre', 'gmat']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('test_scores', function (Blueprint $table) {
            $table->string('sat_i')->nullable()->after('score_3');
            $table->string('sat_ii')->nullable()->after('sat_i');
            $table->string('gre')->nullable()->after('sat_ii');
            $table->string('gmat')->nullable()->after('gre');
        });
    }
};
