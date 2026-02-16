<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Staff-specific columns - these now live in the staff table only.
     * Admins table holds clients (role 7) and minimal auth data for staff.
     */
    protected array $staffColumns = [
        'position',
        'team',
        'permission',
        'time_zone',
        'email_signature',
    ];

    /**
     * Do NOT drop these - needed for client alternate contact until migrated
     * to client_alternate_contacts.
     */
    protected array $columnsToKeep = ['att_email', 'att_country_code', 'att_phone'];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('admins')) {
            return;
        }

        $toDrop = array_filter($this->staffColumns, fn (string $col) => Schema::hasColumn('admins', $col));
        $toDrop = array_diff($toDrop, $this->columnsToKeep);
        if (!empty($toDrop)) {
            Schema::table('admins', function (Blueprint $table) use ($toDrop) {
                $table->dropColumn($toDrop);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('admins')) {
            return;
        }

        Schema::table('admins', function (Blueprint $table) {
            foreach ($this->staffColumns as $col) {
                if (!Schema::hasColumn('admins', $col)) {
                    if (in_array($col, ['position', 'team'])) {
                        $table->string($col, 255)->nullable();
                    } elseif ($col === 'time_zone') {
                        $table->string($col, 50)->nullable();
                    } elseif (in_array($col, ['permission', 'email_signature'])) {
                        $table->text($col)->nullable();
                    }
                }
            }
        });
    }
};
