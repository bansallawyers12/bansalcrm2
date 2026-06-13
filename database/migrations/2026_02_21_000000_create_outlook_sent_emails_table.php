<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Stores every email sent from Admin Outlook (AWS SES) so Sent folder shows
     * which message was sent from which email, like Outlook.
     */
    public function up(): void
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outlook_sent_emails');
    }
};
