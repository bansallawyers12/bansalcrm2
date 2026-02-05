<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Drops the fee_types table. Fee Type feature has been removed from Admin Console.
     * Product fee type dropdowns now use static "Tution Fees" instead of this table.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::dropIfExists('fee_types');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::create('fee_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
    }
};
