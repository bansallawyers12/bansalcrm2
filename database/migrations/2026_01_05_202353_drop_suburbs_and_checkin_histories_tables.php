<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop checkin_histories table
        Schema::dropIfExists('checkin_histories');
        
        // Drop suburbs table
        Schema::dropIfExists('suburbs');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: This migration does not restore the tables as the original schema is unknown
        // If rollback is needed, the original table creation migrations would need to be restored
    }
};
