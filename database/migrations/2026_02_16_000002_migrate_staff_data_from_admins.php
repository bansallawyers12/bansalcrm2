<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Copies staff data from admins to staff table for all admins where role != 7.
     * Skips admins that already have a staff record.
     */
    public function up(): void
    {
        $staffCols = [
            'staff_id', 'office_id', 'position', 'team', 'permission',
            'show_dashboard_per', 'time_zone', 'telephone', 'att_email',
            'att_country_code', 'att_phone', 'email_signature',
        ];

        $adminsColumns = Schema::getColumnListing('admins');
        $colsToCopy = array_filter($staffCols, fn ($c) => in_array($c, $adminsColumns));

        $selectParts = ['a.id AS admin_id'];
        foreach ($colsToCopy as $col) {
            $selectParts[] = 'a.' . $col;
        }
        $selectParts[] = 'a.created_at';
        $selectParts[] = 'a.updated_at';

        $insertCols = ['admin_id', ...$colsToCopy, 'created_at', 'updated_at'];

        $sql = sprintf(
            "INSERT INTO staff (%s) SELECT %s FROM admins a WHERE a.role != 7 AND NOT EXISTS (SELECT 1 FROM staff s WHERE s.admin_id = a.id)",
            implode(', ', $insertCols),
            implode(', ', $selectParts)
        );

        DB::statement($sql);
    }

    /**
     * Reverse the migrations.
     * Removes staff records that were migrated from admins (role != 7).
     */
    public function down(): void
    {
        $adminIds = DB::table('admins')->where('role', '!=', 7)->pluck('id');
        DB::table('staff')->whereIn('admin_id', $adminIds)->delete();
    }
};
