<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('elite_email_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('elite_email_id')->constrained('elite_emails')->cascadeOnDelete();
            $table->string('form_field', 64)->nullable();
            $table->string('original_filename', 512)->nullable();
            $table->string('mime_type', 255)->nullable();
            $table->unsignedBigInteger('size_bytes')->default(0);
            $table->string('content_id', 512)->nullable()->index();
            $table->string('storage_path', 1024);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('elite_email_attachments');
    }
};
