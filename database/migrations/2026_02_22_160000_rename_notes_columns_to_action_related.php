<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Rename notes table columns for better action-related identification:
     * - folloup -> is_action (fix typo, action-centric naming)
     * - followup_date -> action_assign_date (matches UI "Assign Date")
     */
    public function up(): void
    {
        if (!Schema::hasTable('notes')) {
            return;
        }

        Schema::table('notes', function (Blueprint $table) {
            if (Schema::hasColumn('notes', 'folloup')) {
                $table->renameColumn('folloup', 'is_action');
            }
            if (Schema::hasColumn('notes', 'followup_date')) {
                $table->renameColumn('followup_date', 'action_assign_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('notes')) {
            return;
        }

        Schema::table('notes', function (Blueprint $table) {
            if (Schema::hasColumn('notes', 'is_action')) {
                $table->renameColumn('is_action', 'folloup');
            }
            if (Schema::hasColumn('notes', 'action_assign_date')) {
                $table->renameColumn('action_assign_date', 'followup_date');
            }
        });
    }
};
