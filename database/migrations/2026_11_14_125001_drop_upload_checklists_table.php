<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Drop the unused shared upload-checklist library.
     * Client document checklists and the ongoing-sheet Send checklist email stay.
     */
    public function up(): void
    {
        Schema::dropIfExists('upload_checklists');
    }

    /**
     * Recreate the empty library table. Files that lived under public/checklists are not restored.
     */
    public function down(): void
    {
        Schema::create('upload_checklists', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('file')->nullable();
            $table->timestamps();
        });
    }
};
