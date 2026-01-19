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
        Schema::create('signers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_id');
            $table->string('email');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->string('status', 20)->default('pending'); // pending, signed, cancelled
            $table->timestamp('signed_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('last_reminder_sent_at')->nullable();
            $table->unsignedTinyInteger('reminder_count')->default(0);
            $table->string('email_template')->nullable();
            $table->string('email_subject')->nullable();
            $table->text('email_message')->nullable();
            $table->string('from_email')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('document_id');
            $table->index('email');
            $table->index('status');
            $table->index('token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signers');
    }
};
