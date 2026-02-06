<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Status for Checklist sheet: active, convert_to_client, discontinue, hold.
     * Null = treat as active. convert_to_client / discontinue = row leaves Checklist.
     */
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->string('checklist_sheet_status', 32)->nullable()->after('status')
                ->comment('Checklist sheet: active, convert_to_client, discontinue, hold');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn('checklist_sheet_status');
        });
    }
};
