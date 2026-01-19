<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('signature_fields', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_id');
            $table->unsignedBigInteger('signer_id')->nullable();
            $table->unsignedInteger('page_number');
            $table->float('x_position')->nullable();
            $table->float('y_position')->nullable();
            $table->float('x_percent')->nullable();
            $table->float('y_percent')->nullable();
            $table->float('width_percent')->nullable();
            $table->float('height_percent')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('document_id');
            $table->index('signer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signature_fields');
    }
};
