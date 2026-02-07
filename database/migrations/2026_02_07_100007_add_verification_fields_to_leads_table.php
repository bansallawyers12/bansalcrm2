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
        Schema::table('leads', function (Blueprint $table) {
            if (!Schema::hasColumn('leads', 'is_verified')) {
                $table->boolean('is_verified')->default(false);
            }
            if (!Schema::hasColumn('leads', 'verified_at')) {
                $table->timestamp('verified_at')->nullable();
            }
            if (!Schema::hasColumn('leads', 'verified_by')) {
                $table->unsignedBigInteger('verified_by')->nullable();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('leads')) {
            return;
        }
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['is_verified', 'verified_at', 'verified_by']);
        });
    }
};
