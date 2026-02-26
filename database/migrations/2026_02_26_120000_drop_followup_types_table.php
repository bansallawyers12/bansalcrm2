<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Drops followup_types table - unused; lead status uses string values (Unassigned, Assigned, etc.) from client edit page.
     */
    public function up(): void
    {
        if (Schema::hasTable('followup_types')) {
            Schema::drop('followup_types');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('followup_types', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable();
            $table->string('name')->nullable();
            $table->timestamps();
        });
    }
};
