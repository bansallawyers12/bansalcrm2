<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('elite_emails', function (Blueprint $table) {
            $table->id();
            $table->string('from_address', 255);
            $table->string('to_address', 255)->nullable();
            $table->string('subject', 998)->nullable();
            $table->longText('body_text')->nullable();
            $table->longText('body_html')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->index('from_address');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('elite_emails');
    }
};
