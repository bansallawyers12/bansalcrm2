<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            if (! Schema::hasColumn('staff', 'crm_full_access')) {
                $table->boolean('crm_full_access')->default(false)->after('quick_access_enabled');
            }
            if (! Schema::hasColumn('staff', 'crm_access_approver')) {
                $table->boolean('crm_access_approver')->default(false)->after('crm_full_access');
            }
        });
    }

    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            if (Schema::hasColumn('staff', 'crm_access_approver')) {
                $table->dropColumn('crm_access_approver');
            }
            if (Schema::hasColumn('staff', 'crm_full_access')) {
                $table->dropColumn('crm_full_access');
            }
        });
    }
};
