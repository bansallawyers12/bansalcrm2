<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Delegates to Artisan command for progress bar and chunking.
     * Run manually: php artisan leads:migrate-to-admins
     * Use --limit=500 for batch runs. Use --chunk=200 for memory.
     */
    public function up(): void
    {
        $output = new \Symfony\Component\Console\Output\StreamOutput(fopen('php://stdout', 'w'));
        Artisan::call('leads:migrate-to-admins', [], $output);
    }

    /**
     * Reverse the migrations.
     * No-op: data migration cannot be fully reversed.
     */
    public function down(): void
    {
        // No-op
    }
};
