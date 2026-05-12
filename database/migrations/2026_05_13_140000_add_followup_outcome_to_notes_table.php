<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('notes')) {
            return;
        }

        Schema::table('notes', function (Blueprint $table) {
            if (! Schema::hasColumn('notes', 'followup_outcome')) {
                $table->string('followup_outcome', 32)->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('notes')) {
            return;
        }

        Schema::table('notes', function (Blueprint $table) {
            if (Schema::hasColumn('notes', 'followup_outcome')) {
                $table->dropColumn('followup_outcome');
            }
        });
    }
};
