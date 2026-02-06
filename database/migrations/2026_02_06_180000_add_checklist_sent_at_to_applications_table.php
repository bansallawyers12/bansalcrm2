<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Date when checklist email was sent to client (date only for display).
     */
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->date('checklist_sent_at')->nullable()->after('checklist_sheet_status')
                ->comment('Date when checklist was sent to client; updated on resend');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn('checklist_sent_at');
        });
    }
};
