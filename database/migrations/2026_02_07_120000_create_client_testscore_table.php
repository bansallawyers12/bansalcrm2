<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Matches migrationmanager2: one row per test, generic listening/reading/writing/speaking/overall_score.
     */
    public function up(): void
    {
        Schema::create('client_testscore', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->string('test_type', 100)->nullable()->comment('e.g. IELTS, IELTS Academic, PTE, TOEFL, CAE, OET, CELPIP General, Michigan English Test (MET), LANGUAGECERT Academic');
            $table->string('listening', 20)->nullable();
            $table->string('reading', 20)->nullable();
            $table->string('writing', 20)->nullable();
            $table->string('speaking', 20)->nullable();
            $table->string('overall_score', 20)->nullable();
            $table->string('proficiency_level', 100)->nullable();
            $table->integer('proficiency_points')->nullable();
            $table->date('test_date')->nullable();
            $table->boolean('relevant_test')->nullable();
            $table->string('test_reference_no', 100)->nullable();
            $table->timestamps();

            $table->index('client_id');
            $table->index('admin_id');
            $table->index('test_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_testscore');
    }
};
