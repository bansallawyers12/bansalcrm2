<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Unused columns to drop from agents table:
     * - password, remember_token: Agents don't have login access
     * - profile_img: Never persisted by AgentController
     */
    protected array $columnsToDrop = ['password', 'remember_token', 'profile_img'];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('agents')) {
            return;
        }

        $toDrop = array_filter($this->columnsToDrop, fn (string $col) => Schema::hasColumn('agents', $col));

        if (!empty($toDrop)) {
            Schema::table('agents', function (Blueprint $table) use ($toDrop) {
                $table->dropColumn($toDrop);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('agents')) {
            return;
        }

        Schema::table('agents', function (Blueprint $table) {
            if (!Schema::hasColumn('agents', 'password')) {
                $table->string('password')->nullable();
            }
            if (!Schema::hasColumn('agents', 'remember_token')) {
                $table->string('remember_token', 100)->nullable();
            }
            if (!Schema::hasColumn('agents', 'profile_img')) {
                $table->string('profile_img')->nullable();
            }
        });
    }
};
