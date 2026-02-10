<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LeadsColumnStats extends Command
{
    protected $signature = 'leads:column-stats';

    protected $description = 'Report non-null counts per column in leads table (for zero-data review)';

    public function handle(): int
    {
        if (!Schema::hasTable('leads')) {
            $this->error('Table leads does not exist.');
            return 1;
        }

        $columns = Schema::getColumnListing('leads');
        $total = DB::table('leads')->count();

        if ($total === 0) {
            $this->warn('Table leads is empty.');
            return 0;
        }

        $stats = [];
        foreach ($columns as $col) {
            $filled = (int) DB::table('leads')->whereNotNull($col)->count();
            $stats[$col] = ['filled' => $filled, 'null' => $total - $filled];
        }

        $zeroData = array_filter($stats, fn($s) => $s['filled'] === 0);

        $this->info("leads table: {$total} rows, " . count($columns) . " columns");
        $this->line('');
        $this->info('Columns with ZERO data (all NULL): ' . count($zeroData));
        $this->table(['Column', 'Filled', 'Null'], array_map(fn($col, $s) => [$col, $s['filled'], $s['null']], array_keys($zeroData), $zeroData));

        return 0;
    }
}
