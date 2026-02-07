<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('phone_verifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_phone_id')->nullable()->comment('ClientPhone when verifying client');
            $table->unsignedBigInteger('lead_id')->nullable()->comment('Lead when verifying lead phone');
            $table->unsignedBigInteger('client_id')->nullable()->comment('Client (admin) id when client_phone_id set');
            $table->string('phone', 20);
            $table->string('country_code', 10);
            $table->string('otp_code', 6);
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->timestamp('otp_sent_at')->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->integer('attempts')->default(0);
            $table->integer('max_attempts')->default(3);
            $table->timestamps();

            $table->index('client_phone_id');
            $table->index('lead_id');
            $table->index('otp_code');
            $table->index(['phone', 'country_code']);
            $table->index('otp_expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('phone_verifications');
    }
};
