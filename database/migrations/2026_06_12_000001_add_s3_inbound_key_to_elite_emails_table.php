<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('elite_emails', function (Blueprint $table) {
            // Stores the S3 object key of the source .eml file.
            // Used for deduplication so re-running the sync command never double-imports.
            $table->string('s3_inbound_key', 1024)
                ->nullable()
                ->unique()
                ->after('body_html_s3_key');
        });
    }

    public function down(): void
    {
        Schema::table('elite_emails', function (Blueprint $table) {
            $table->dropUnique(['s3_inbound_key']);
            $table->dropColumn('s3_inbound_key');
        });
    }
};
