<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('leads')) {
            return;
        }

        $columnsToDrop = [
            'profile_img',
            'preferredintake',
            'lead_id',
            'social_type',
            'social_link',
            'advertisements_name',
        ];

        Schema::table('leads', function (Blueprint $table) use ($columnsToDrop) {
            foreach ($columnsToDrop as $col) {
                if (Schema::hasColumn('leads', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('leads')) {
            return;
        }

        Schema::table('leads', function (Blueprint $table) {
            if (!Schema::hasColumn('leads', 'profile_img')) {
                $table->string('profile_img')->nullable();
            }
            if (!Schema::hasColumn('leads', 'preferredintake')) {
                $table->date('preferredintake')->nullable();
            }
            if (!Schema::hasColumn('leads', 'lead_id')) {
                $table->bigInteger('lead_id')->nullable();
            }
            if (!Schema::hasColumn('leads', 'social_type')) {
                $table->string('social_type')->nullable();
            }
            if (!Schema::hasColumn('leads', 'social_link')) {
                $table->text('social_link')->nullable();
            }
            if (!Schema::hasColumn('leads', 'advertisements_name')) {
                $table->string('advertisements_name')->nullable();
            }
        });
    }
};
