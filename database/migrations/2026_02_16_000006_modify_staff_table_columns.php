<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Removes staff_id, is_archived, archived_by, archived_on.
     * Moves role column next to id.
     */
    public function up(): void
    {
        if (!Schema::hasTable('staff')) {
            return;
        }

        Schema::table('staff', function (Blueprint $table) {
            $table->dropForeign(['archived_by']);
        });

        Schema::table('staff', function (Blueprint $table) {
            $columnsToDrop = ['staff_id', 'is_archived', 'archived_by', 'archived_on'];
            $existing = array_filter($columnsToDrop, fn ($c) => Schema::hasColumn('staff', $c));
            if (!empty($existing)) {
                $table->dropColumn($existing);
            }
        });

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            Schema::table('staff', function (Blueprint $table) {
                $table->dropForeign(['role']);
            });
            DB::statement('ALTER TABLE staff MODIFY COLUMN role INT NULL AFTER id');
            Schema::table('staff', function (Blueprint $table) {
                if (Schema::hasTable('user_roles')) {
                    $table->foreign('role')->references('id')->on('user_roles')->onDelete('set null');
                }
            });
        }
        // PostgreSQL: column order cannot be changed without full table recreation
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('staff')) {
            return;
        }

        Schema::table('staff', function (Blueprint $table) {
            $table->string('staff_id', 100)->nullable()->after('password');
            $table->tinyInteger('is_archived')->default(0)->after('email_signature');
            $table->unsignedBigInteger('archived_by')->nullable()->after('is_archived');
            $table->timestamp('archived_on')->nullable()->after('archived_by');
        });

        Schema::table('staff', function (Blueprint $table) {
            $table->foreign('archived_by')->references('id')->on('staff')->onDelete('set null');
        });

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            Schema::table('staff', function (Blueprint $table) {
                $table->dropForeign(['role']);
            });
            DB::statement('ALTER TABLE staff MODIFY COLUMN role INT NULL AFTER show_dashboard_per');
            Schema::table('staff', function (Blueprint $table) {
                if (Schema::hasTable('user_roles')) {
                    $table->foreign('role')->references('id')->on('user_roles')->onDelete('set null');
                }
            });
        }
    }
};
