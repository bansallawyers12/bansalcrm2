<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Drops unused columns from contacts table (subject, message, image, ip_address).
     */
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn(['subject', 'message', 'image', 'ip_address']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('subject')->nullable()->after('department');
            $table->text('message')->nullable()->after('subject');
            $table->string('image')->nullable()->after('message');
            $table->string('ip_address')->nullable()->after('user_id');
        });
    }
};
