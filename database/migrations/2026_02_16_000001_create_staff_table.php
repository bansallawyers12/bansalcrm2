<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates a staff table with staff-related columns extracted from admins.
     * Links to admins via admin_id (staff are admins with role != 7).
     */
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id')->unique()->comment('FK to admins.id - one staff record per admin');
            $table->string('staff_id', 100)->nullable()->comment('External staff identifier');
            $table->unsignedBigInteger('office_id')->nullable()->comment('FK to branches.id - branch/office assignment');
            $table->string('position', 100)->nullable()->comment('Job title/position');
            $table->string('team', 100)->nullable()->comment('Team assignment');
            $table->string('permission')->nullable()->comment('Access permissions');
            $table->string('show_dashboard_per')->nullable()->comment('Dashboard visibility permissions');
            $table->string('time_zone', 50)->nullable()->comment('Working timezone');
            $table->string('telephone', 50)->nullable()->comment('Office/desk phone');
            $table->string('att_email')->nullable()->comment('Attendant/alternate email');
            $table->string('att_country_code', 10)->nullable()->comment('Attendant phone country code');
            $table->string('att_phone', 50)->nullable()->comment('Attendant/alternate phone');
            $table->text('email_signature')->nullable()->comment('Email signature for outgoing emails');
            $table->timestamps();

            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
            if (Schema::hasTable('branches')) {
                $table->foreign('office_id')->references('id')->on('branches')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
