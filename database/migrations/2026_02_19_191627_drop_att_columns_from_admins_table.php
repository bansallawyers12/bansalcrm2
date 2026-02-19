<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Columns to drop from admins table.
     * Data should have been migrated to client_emails and client_phones by earlier migrations
     * (2026_02_16_200012_migrate_att_email_to_client_emails and 2026_02_16_200009_migrate_att_phone_to_client_phones).
     */
    protected array $columnsToDrop = ['att_email', 'att_country_code', 'att_phone'];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('admins')) {
            return;
        }

        $toDrop = array_filter($this->columnsToDrop, fn (string $col) => Schema::hasColumn('admins', $col));

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
            foreach ($this->columnsToDrop as $col) {
                if (!Schema::hasColumn('admins', $col)) {
                    if ($col === 'att_email') {
                        $table->string('att_email')->nullable();
                    } elseif ($col === 'att_country_code') {
                        $table->string('att_country_code', 10)->nullable();
                    } elseif ($col === 'att_phone') {
                        $table->string('att_phone', 50)->nullable();
                    }
                }
            }
        });
    }
};
