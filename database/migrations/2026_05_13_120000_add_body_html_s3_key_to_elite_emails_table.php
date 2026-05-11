<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('elite_emails', function (Blueprint $table) {
            $table->string('body_html_s3_key', 1024)->nullable()->after('body_html');
        });
    }

    public function down(): void
    {
        Schema::table('elite_emails', function (Blueprint $table) {
            $table->dropColumn('body_html_s3_key');
        });
    }
};
