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
     * The admin guard uses Staff model (staff table) for authentication.
     * checkin_histories.created_by stores the logged-in user id, which is Staff.id.
     * The previous FK referenced admins table, causing "Key (created_by)=(1) is not present in admins" error.
     */
    public function up(): void
    {
        Schema::table('checkin_histories', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
        });

        // Fix orphaned created_by: any value not in staff gets set to first staff id (for legacy admins refs)
        $firstStaffId = DB::table('staff')->orderBy('id')->value('id');
        if ($firstStaffId !== null) {
            DB::table('checkin_histories')
                ->whereNotIn('created_by', DB::table('staff')->pluck('id'))
                ->update(['created_by' => $firstStaffId]);
        }

        Schema::table('checkin_histories', function (Blueprint $table) {
            $table->foreign('created_by')
                  ->references('id')
                  ->on('staff')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('checkin_histories', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
        });

        Schema::table('checkin_histories', function (Blueprint $table) {
            $table->foreign('created_by')
                  ->references('id')
                  ->on('admins')
                  ->onDelete('cascade');
        });
    }
};
