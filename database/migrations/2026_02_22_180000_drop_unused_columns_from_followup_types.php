<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Drops unused columns from followup_types: icon, color, show.
     */
    public function up(): void
    {
        if (!Schema::hasTable('followup_types')) {
            return;
        }

        $columnsToDrop = ['icon', 'color', 'show'];

        Schema::table('followup_types', function (Blueprint $table) use ($columnsToDrop) {
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('followup_types', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('followup_types')) {
            return;
        }

        Schema::table('followup_types', function (Blueprint $table) {
            if (!Schema::hasColumn('followup_types', 'icon')) {
                $table->string('icon')->nullable()->after('type');
            }
            if (!Schema::hasColumn('followup_types', 'color')) {
                $table->string('color')->nullable()->after('icon');
            }
            if (!Schema::hasColumn('followup_types', 'show')) {
                $table->tinyInteger('show')->nullable()->after('color');
            }
        });
    }
};
