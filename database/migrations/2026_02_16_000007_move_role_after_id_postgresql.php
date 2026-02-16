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
     * Moves role column next to id in PostgreSQL (recreates table with correct order).
     */
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'pgsql' || !Schema::hasTable('staff')) {
            return;
        }

        if (Schema::getColumnType('staff', 'role') === null) {
            return;
        }

        $cols = Schema::getColumnListing('staff');
        if (array_search('role', $cols) === 1) {
            return; // role already second
        }

        DB::transaction(function () {
            Schema::table('staff', function (Blueprint $table) {
                $table->dropForeign(['role']);
            });

            Schema::create('staff_new', function (Blueprint $table) {
                $table->id();
                $table->integer('role')->nullable();
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->string('email')->unique();
                $table->string('password');
                $table->string('country_code', 20)->nullable();
                $table->string('phone', 100)->nullable();
                $table->tinyInteger('status')->default(1);
                $table->tinyInteger('verified')->default(0);
                $table->string('position', 255)->nullable();
                $table->string('team', 255)->nullable();
                $table->text('permission')->nullable();
                $table->unsignedBigInteger('office_id')->nullable();
                $table->tinyInteger('show_dashboard_per')->default(0);
                $table->string('time_zone', 50)->nullable();
                $table->text('email_signature')->nullable();
                $table->rememberToken();
                $table->timestamps();
            });

            DB::statement('INSERT INTO staff_new SELECT id, role, first_name, last_name, email, password, country_code, phone, status, verified, position, team, permission, office_id, show_dashboard_per, time_zone, email_signature, remember_token, created_at, updated_at FROM staff');

            Schema::drop('staff');
            Schema::rename('staff_new', 'staff');

            $maxId = DB::table('staff')->max('id') ?? 0;
            DB::statement("SELECT setval(pg_get_serial_sequence('staff', 'id'), ?)", [$maxId]);

            Schema::table('staff', function (Blueprint $table) {
                if (Schema::hasTable('branches')) {
                    $table->foreign('office_id')->references('id')->on('branches')->onDelete('set null');
                }
                if (Schema::hasTable('user_roles')) {
                    $table->foreign('role')->references('id')->on('user_roles')->onDelete('set null');
                }
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op; column order change is cosmetic
    }
};
