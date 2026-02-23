<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Followups table removed - system unused; lead status from admins.status (string values: Unassigned, Assigned, etc.).
     */
    public function up(): void
    {
        Schema::dropIfExists('followups');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('followups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_id')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->string('followup_type')->nullable();
            $table->dateTime('followup_date')->nullable();
            $table->timestamps();
        });
    }
};
