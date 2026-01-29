<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Stores which test type (toefl/ilets/pte) was last saved so the edit page shows it correctly.
     */
    public function up(): void
    {
        Schema::table('test_scores', function (Blueprint $table) {
            $table->string('last_test_type', 20)->nullable()->after('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('test_scores', function (Blueprint $table) {
            $table->dropColumn('last_test_type');
        });
    }
};
