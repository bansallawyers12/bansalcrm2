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
            if (! Schema::hasColumn('emails', 'python_rendering')) {
                $table->json('python_rendering')->nullable();
            }

            if (! Schema::hasColumn('emails', 'enhanced_html')) {
                $table->longText('enhanced_html')->nullable();
            }

            if (! Schema::hasColumn('emails', 'rendered_html')) {
                $table->longText('rendered_html')->nullable();
            }

            if (! Schema::hasColumn('emails', 'text_preview')) {
                $table->text('text_preview')->nullable();
            }

            if (! Schema::hasColumn('emails', 'last_accessed_at')) {
                $table->timestamp('last_accessed_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('emails')) {
            return;
        }

        Schema::table('emails', function (Blueprint $table) {
            $columns = array_values(array_filter([
                Schema::hasColumn('emails', 'python_rendering') ? 'python_rendering' : null,
                Schema::hasColumn('emails', 'enhanced_html') ? 'enhanced_html' : null,
                Schema::hasColumn('emails', 'rendered_html') ? 'rendered_html' : null,
                Schema::hasColumn('emails', 'text_preview') ? 'text_preview' : null,
                Schema::hasColumn('emails', 'last_accessed_at') ? 'last_accessed_at' : null,
            ]));

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
