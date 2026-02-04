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
        Schema::create('client_ongoing_references', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->unique()->comment('FK to admins.id - one row per client');
            
            // Core ongoing sheet fields
            $table->text('current_status')->nullable()->comment('Date-prefixed status notes (e.g., "04/02: Waiting for LOF & Payment")');
            $table->string('payment_display_note', 100)->nullable()->comment('Override for payment display (e.g., "Deferment", "VOE Client")');
            
            // Optional override fields (for future use)
            $table->string('institute_override', 255)->nullable()->comment('Manual institute override if not from applications/service_takens');
            $table->string('visa_category_override', 50)->nullable()->comment('Manual visa category override if not from admins.visa_type');
            
            // Internal notes (not displayed in sheet)
            $table->text('notes')->nullable()->comment('Internal notes for reference');
            
            // Audit fields
            $table->unsignedBigInteger('created_by')->nullable()->comment('Admin who created this record');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Admin who last updated this record');
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('client_id')->references('id')->on('admins')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('admins')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('admins')->onDelete('set null');
            
            // Indexes
            $table->index('updated_at', 'idx_ongoing_updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_ongoing_references');
    }
};
