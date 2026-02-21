<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds lead-specific columns to admins table so that all leads data can be migrated.
     * Skips columns that already exist with different names:
     *   - passport_no → passport_number
     *   - visa_expiry_date → visaexpiry
     *   - lead_source → source
     *   - assign_to → assignee
     *   - tags_label → tags/tagname
     * att_phone, att_email, att_country_code: dropped from admins by design; data goes to client_emails/client_phones.
     */
    public function up(): void
    {
        if (!Schema::hasTable('admins')) {
            return;
        }

        Schema::table('admins', function (Blueprint $table) {
            if (!Schema::hasColumn('admins', 'converted')) {
                $table->boolean('converted')->default(false)->after('lead_id')->comment('Lead converted to client');
            }
            if (!Schema::hasColumn('admins', 'converted_date')) {
                $table->date('converted_date')->nullable()->after('converted')->comment('Date lead was converted to client');
            }
            if (!Schema::hasColumn('admins', 'is_verified')) {
                $table->boolean('is_verified')->default(false)->after('verified')->comment('Phone verification status (for leads)');
            }
            if (!Schema::hasColumn('admins', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('is_verified')->comment('Phone verified at');
            }
            if (!Schema::hasColumn('admins', 'verified_by')) {
                $table->unsignedBigInteger('verified_by')->nullable()->after('verified_at')->comment('FK to staff/admin who verified phone');
            }
        });
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
            $columnsToDrop = array_filter(
                ['converted', 'converted_date', 'is_verified', 'verified_at', 'verified_by'],
                fn (string $col) => Schema::hasColumn('admins', $col)
            );

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
