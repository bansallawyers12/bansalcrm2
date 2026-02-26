<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Stores draft emails from Admin Outlook so they can be resumed later.
     */
    public function up(): void
    {
        Schema::create('outlook_draft_emails', function (Blueprint $table) {
            $table->id();
            $table->string('from_email');
            $table->string('to_email')->nullable();
            $table->text('cc')->nullable();
            $table->string('subject')->nullable();
            $table->longText('body')->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->timestamps();

            $table->index('admin_id');
            $table->index('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outlook_draft_emails');
    }
};
