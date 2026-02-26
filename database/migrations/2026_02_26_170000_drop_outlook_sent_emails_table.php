<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Drops outlook_sent_emails - sent emails now stored in mail_reports.
     */
    public function up(): void
    {
        Schema::dropIfExists('outlook_sent_emails');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('outlook_sent_emails', function (Blueprint $table) {
            $table->id();
            $table->string('from_email');
            $table->string('to_email');
            $table->text('cc')->nullable();
            $table->string('subject');
            $table->longText('body')->nullable();
            $table->timestamp('sent_at');
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->timestamps();

            $table->index('from_email');
            $table->index('sent_at');
            $table->index('admin_id');
        });
    }
};
