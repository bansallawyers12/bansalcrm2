<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates standalone staff table with all staff-specific columns from admins (Option A).
     * Staff = admins where role != 7. staff.id preserves admins.id.
     */
    public function up(): void
    {
        if (Schema::hasTable('staff')) {
            return;
        }

        Schema::create('staff', function (Blueprint $table) {
            $table->id();

            // Core identity
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('staff_id', 100)->nullable()->comment('External staff identifier');

            // Contact
            $table->string('country_code', 20)->nullable();
            $table->string('phone', 100)->nullable();
            $table->string('telephone', 100)->nullable();
            $table->string('att_email')->nullable();
            $table->string('att_country_code', 10)->nullable();
            $table->string('att_phone', 50)->nullable();

            // Status
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('verified')->default(0);

            // Staff-specific (AdminConsole)
            $table->integer('role')->nullable()->comment('FK to user_roles.id');
            $table->string('position', 255)->nullable();
            $table->string('team', 255)->nullable();
            $table->text('permission')->nullable();
            $table->unsignedBigInteger('office_id')->nullable()->comment('FK to branches.id');
            $table->tinyInteger('show_dashboard_per')->default(0);
            $table->string('time_zone', 50)->nullable();

            // Other staff fields
            $table->text('email_signature')->nullable();

            // Archive
            $table->tinyInteger('is_archived')->default(0);
            $table->unsignedBigInteger('archived_by')->nullable()->comment('FK to staff.id');
            $table->timestamp('archived_on')->nullable();

            $table->rememberToken();
            $table->timestamps();
        });

        Schema::table('staff', function (Blueprint $table) {
            if (Schema::hasTable('branches')) {
                $table->foreign('office_id')->references('id')->on('branches')->onDelete('set null');
            }
            if (Schema::hasTable('user_roles')) {
                $table->foreign('role')->references('id')->on('user_roles')->onDelete('set null');
            }
        });

        Schema::table('staff', function (Blueprint $table) {
            $table->foreign('archived_by')->references('id')->on('staff')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->dropForeign(['archived_by']);
        });
        Schema::dropIfExists('staff');
    }
};
