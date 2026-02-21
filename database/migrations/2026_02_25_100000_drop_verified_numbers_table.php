<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Drop legacy verified_numbers table.
     * Phone verification is now stored in client_phones.is_verified and phone_verifications.
     */
    public function up(): void
    {
        Schema::dropIfExists('verified_numbers');
    }

    public function down(): void
    {
        Schema::create('verified_numbers', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number');
            $table->boolean('is_verified')->default(false);
            $table->string('verification_code')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }
};
