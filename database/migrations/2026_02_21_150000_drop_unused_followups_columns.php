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
        Schema::table('followups', function (Blueprint $table) {
            $table->dropColumn(['subject', 'rem_cat', 'pin', 'user_id', 'note']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('followups', function (Blueprint $table) {
            $table->string('subject')->nullable();
            $table->string('rem_cat')->nullable();
            $table->tinyInteger('pin')->default(0);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->text('note')->nullable();
        });
    }
};
