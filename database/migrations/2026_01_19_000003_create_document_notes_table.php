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
        Schema::create('document_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_id');
            $table->unsignedBigInteger('created_by')->comment('Admin who performed the action');
            $table->string('action_type', 50)->comment('associated, detached, status_changed, email_sent, etc.');
            $table->text('note')->nullable()->comment('User-provided note or system-generated description');
            $table->json('metadata')->nullable()->comment('Additional data: entity type/id, old values, etc.');
            $table->timestamps();

            // Indexes
            $table->index('document_id');
            $table->index('created_by');
            $table->index('action_type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_notes');
    }
};
