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
        Schema::create('test_scores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->string('type')->nullable();
            
            // TOEFL scores
            $table->string('toefl_Listening')->nullable();
            $table->string('toefl_Reading')->nullable();
            $table->string('toefl_Writing')->nullable();
            $table->string('toefl_Speaking')->nullable();
            $table->date('toefl_Date')->nullable();
            
            // IELTS scores
            $table->string('ilets_Listening')->nullable();
            $table->string('ilets_Reading')->nullable();
            $table->string('ilets_Writing')->nullable();
            $table->string('ilets_Speaking')->nullable();
            $table->date('ilets_Date')->nullable();
            
            // PTE scores
            $table->string('pte_Listening')->nullable();
            $table->string('pte_Reading')->nullable();
            $table->string('pte_Writing')->nullable();
            $table->string('pte_Speaking')->nullable();
            $table->date('pte_Date')->nullable();
            
            // Overall scores
            $table->string('score_1')->nullable();
            $table->string('score_2')->nullable();
            $table->string('score_3')->nullable();
            
            // Other test scores
            $table->string('sat_i')->nullable();
            $table->string('sat_ii')->nullable();
            $table->string('gre')->nullable();
            $table->string('gmat')->nullable();
            
            $table->timestamps();
            
            // Add indexes
            $table->index('user_id');
            $table->index('client_id');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_scores');
    }
};
