<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_day_summaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id');
            $table->date('summary_date');
            $table->text('body');
            $table->string('source', 16)->default('copy');
            $table->timestamp('saved_at')->nullable();
            $table->timestamps();

            $table->foreign('staff_id')->references('id')->on('staff')->cascadeOnDelete();
            $table->unique(['staff_id', 'summary_date']);
            $table->index('summary_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_day_summaries');
    }
};
