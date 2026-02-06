<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class RenameMartialStatusToMaritalStatusAndNormalize extends Migration
{
    /**
     * Run the migrations.
     * Renames martial_status -> marital_status on leads (and admins if present).
     * Normalizes values: De facto/Defacto -> De Facto, Others -> Never Married.
     *
     * @return void
     */
    public function up()
    {
        // Rename column on leads table
        if (Schema::hasTable('leads') && Schema::hasColumn('leads', 'martial_status')) {
            Schema::table('leads', function (Blueprint $table) {
                $table->renameColumn('martial_status', 'marital_status');
            });
        }

        // Rename column on admins table if it exists
        if (Schema::hasTable('admins') && Schema::hasColumn('admins', 'martial_status')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->renameColumn('martial_status', 'marital_status');
            });
        }

        // Normalize marital_status values (run after rename so column is marital_status)
        $driver = DB::getDriverName();

        if (Schema::hasTable('leads') && Schema::hasColumn('leads', 'marital_status')) {
            if ($driver === 'pgsql') {
                DB::statement("UPDATE leads SET marital_status = 'De Facto' WHERE marital_status IN ('De facto', 'Defacto')");
                DB::statement("UPDATE leads SET marital_status = 'Never Married' WHERE marital_status = 'Others'");
            } else {
                DB::table('leads')
                    ->whereIn('marital_status', ['De facto', 'Defacto'])
                    ->update(['marital_status' => 'De Facto']);
                DB::table('leads')
                    ->where('marital_status', 'Others')
                    ->update(['marital_status' => 'Never Married']);
            }
        }

        if (Schema::hasTable('admins') && Schema::hasColumn('admins', 'marital_status')) {
            if ($driver === 'pgsql') {
                DB::statement("UPDATE admins SET marital_status = 'De Facto' WHERE marital_status IN ('De facto', 'Defacto')");
                DB::statement("UPDATE admins SET marital_status = 'Never Married' WHERE marital_status = 'Others'");
            } else {
                DB::table('admins')
                    ->whereIn('marital_status', ['De facto', 'Defacto'])
                    ->update(['marital_status' => 'De Facto']);
                DB::table('admins')
                    ->where('marital_status', 'Others')
                    ->update(['marital_status' => 'Never Married']);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('leads') && Schema::hasColumn('leads', 'marital_status')) {
            Schema::table('leads', function (Blueprint $table) {
                $table->renameColumn('marital_status', 'martial_status');
            });
        }

        if (Schema::hasTable('admins') && Schema::hasColumn('admins', 'marital_status')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->renameColumn('marital_status', 'martial_status');
            });
        }
    }
}
