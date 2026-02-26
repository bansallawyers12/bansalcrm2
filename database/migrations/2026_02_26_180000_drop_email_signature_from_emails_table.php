<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Email signature moved to Staff section (staff.email_signature).
     */
    public function up(): void
    {
        if (Schema::hasTable('emails') && Schema::hasColumn('emails', 'email_signature')) {
            Schema::table('emails', function (Blueprint $table) {
                $table->dropColumn('email_signature');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('emails') && !Schema::hasColumn('emails', 'email_signature')) {
            Schema::table('emails', function (Blueprint $table) {
                $table->text('email_signature')->nullable()->after('display_name');
            });
        }
    }
};
