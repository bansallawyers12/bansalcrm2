<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Remove user_types feature (Option 1).
     * Drops usertype from user_roles, then drops user_types table.
     *
     * @return void
     */
    public function up(): void
    {
        if (Schema::hasTable('user_roles') && Schema::hasColumn('user_roles', 'usertype')) {
            // Drop FK first; use raw SQL with IF EXISTS to avoid errors when constraint missing
            if (DB::getDriverName() === 'pgsql') {
                DB::statement('ALTER TABLE user_roles DROP CONSTRAINT IF EXISTS user_roles_usertype_foreign');
            }
            Schema::table('user_roles', function ($table) {
                $table->dropColumn('usertype');
            });
        }

        Schema::dropIfExists('user_types');
    }

    /**
     * Reverse: cannot reliably restore user_types structure.
     *
     * @return void
     */
    public function down(): void
    {
        // Table structure unknown; restore from backup if needed
    }
};
