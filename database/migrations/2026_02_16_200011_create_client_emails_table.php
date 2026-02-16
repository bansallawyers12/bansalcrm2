<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates client_emails table for storing multiple emails per client.
     * Primary email remains in admins table.
     */
    public function up(): void
    {
        Schema::create('client_emails', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('email_type', 50)->nullable()->comment('Personal, Work, Business, Secondary, etc.');
            $table->string('client_email', 255);
            $table->boolean('is_verified')->nullable()->default(null)->comment('Future use');
            $table->timestamp('verified_at')->nullable()->comment('Future use');
            $table->unsignedBigInteger('verified_by')->nullable()->comment('Future use');
            $table->timestamps();

            $table->index('client_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_emails');
    }
};
