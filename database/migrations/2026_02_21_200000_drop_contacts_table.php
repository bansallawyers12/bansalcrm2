<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Drops the contacts table - feature removed (Manage Contacts and Partner Contacts tab unused).
     */
    public function up(): void
    {
        Schema::dropIfExists('contacts');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('department')->nullable();
            $table->integer('branch')->nullable();
            $table->string('fax')->nullable();
            $table->string('position')->nullable();
            $table->integer('primary_contact')->nullable();
            $table->string('countrycode')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
        });
    }
};
