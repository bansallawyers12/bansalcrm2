<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Restore checkin_histories table for office visit check-in audit trail.
     * This table was previously dropped but is needed to track check-in events,
     * comments, and session changes.
     */
    public function up(): void
    {
        Schema::create('checkin_histories', function (Blueprint $table) {
            $table->id();
            $table->string('subject')->nullable(false);
            $table->unsignedBigInteger('created_by')->nullable(false);
            $table->unsignedBigInteger('checkin_id')->nullable(false);
            $table->text('description')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('checkin_id')
                  ->references('id')
                  ->on('checkin_logs')
                  ->onDelete('cascade');
                  
            $table->foreign('created_by')
                  ->references('id')
                  ->on('admins')
                  ->onDelete('cascade');
            
            // Indexes for performance
            $table->index('checkin_id');
            $table->index('created_by');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checkin_histories');
    }
};
