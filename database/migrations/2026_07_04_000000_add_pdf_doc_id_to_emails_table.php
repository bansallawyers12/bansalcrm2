<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('emails')) {
            return;
        }

        Schema::table('emails', function (Blueprint $table) {
            if (! Schema::hasColumn('emails', 'pdf_doc_id')) {
                $table->unsignedBigInteger('pdf_doc_id')->nullable()->after('uploaded_doc_id');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('emails')) {
            return;
        }

        Schema::table('emails', function (Blueprint $table) {
            if (Schema::hasColumn('emails', 'pdf_doc_id')) {
                $table->dropColumn('pdf_doc_id');
            }
        });
    }
};
