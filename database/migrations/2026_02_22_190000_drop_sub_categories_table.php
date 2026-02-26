<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Drops the unused sub_categories table and its model.
     */
    public function up(): void
    {
        Schema::dropIfExists('sub_categories');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('sub_categories', function (Blueprint $table) {
            $table->id();
            $table->integer('cat_id')->nullable();
            $table->string('name');
            $table->timestamps();
            $table->integer('sub_id')->nullable();
        });
    }
};
