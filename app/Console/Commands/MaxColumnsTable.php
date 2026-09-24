<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MaxColumnsTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:max-columns';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find the table with the maximum number of columns in the PostgreSQL database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            return $this->queryPostgres();
        } catch (\Exception $e) {
            $this->error('Error: '.$e->getMessage());

            return 1;
        }
    }

    /**
     * Query PostgreSQL for table with max columns.
     */
    private function queryPostgres(): int
    {
        $this->info('Querying PostgreSQL database...');
        $this->newLine();

        $result = DB::connection('pgsql')->select("
            SELECT
                table_name,
                COUNT(*) as column_count
            FROM information_schema.columns
            WHERE table_schema = 'public'
            GROUP BY table_name
            ORDER BY column_count DESC
        ");

        if (empty($result)) {
            $this->warn('No tables found in the database.');

            return 0;
        }

        $maxTable = $result[0];
        $this->info('Table with maximum number of columns:');
        $this->info("  Table: {$maxTable->table_name}");
        $this->info("  Column count: {$maxTable->column_count}");
        $this->newLine();

        $this->line('Top 10 tables by column count:');
        $this->table(
            ['Rank', 'Table', 'Columns'],
            array_map(function ($row, $i) {
                return [$i + 1, $row->table_name, $row->column_count];
            }, array_slice($result, 0, 10), array_keys(array_slice($result, 0, 10)))
        );

        return 0;
    }
}
