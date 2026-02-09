<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class ExportAdminsLowDataColumns extends Command
{
    /**
     * Columns with "most data" (>= 40% fill) - excluded from export so you only get low/zero data columns.
     * Based on fill-rate run; id is included in export as row identifier.
     */
    protected const HIGH_DATA_COLUMNS = [
        'role', 'email', 'verified', 'created_at', 'updated_at', 'is_archived',
        'show_dashboard_per', 'type', 'first_name', 'phone', 'last_name', 'client_id',
        'assignee', 'email_type', 'contact_type', 'gender', 'service', 'source',
        'att_country_code', 'country', 'country_passport', 'agent_id', 'lead_quality',
        'status', 'age', 'dob', 'visa_type', 'marital_status', 'visaexpiry', 'city', 'zip', 'state',
    ];

    protected $signature = 'admins:export-low-data-csv
                            {--output= : Optional path under storage/app (default: exports/admins_low_data_columns_{date}.csv)}';

    protected $description = 'Export admins table columns with low or zero data to CSV (excludes high-data columns) for review';

    public function handle(): int
    {
        $table = 'admins';
        if (!Schema::hasTable($table)) {
            $this->error("Table {$table} does not exist.");
            return 1;
        }

        $allColumns = Schema::getColumnListing($table);
        $exportColumns = array_values(array_diff($allColumns, self::HIGH_DATA_COLUMNS));

        if (!in_array('id', $exportColumns, true)) {
            array_unshift($exportColumns, 'id');
        }

        $date = now()->format('Y-m-d_His');
        $defaultPath = "exports/admins_low_data_columns_{$date}.csv";
        $path = $this->option('output') ?: $defaultPath;
        $fullPath = Storage::path($path);

        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $this->info('Exporting ' . count($exportColumns) . ' columns (id + low/zero data columns) to CSV.');
        $this->info('Excluded ' . count(self::HIGH_DATA_COLUMNS) . ' high-data columns.');

        $handle = fopen($fullPath, 'w');
        if ($handle === false) {
            $this->error('Could not open file for writing: ' . $fullPath);
            return 1;
        }

        fputcsv($handle, $exportColumns);

        $chunkSize = 2000;
        $total = 0;
        $query = DB::table($table)->select($exportColumns)->orderBy('id');

        $query->chunk($chunkSize, function ($rows) use ($handle, $exportColumns, &$total) {
            foreach ($rows as $row) {
                $line = [];
                foreach ($exportColumns as $col) {
                    $v = $row->$col ?? '';
                    if ($v !== null && $v !== '') {
                        $line[] = $v;
                    } else {
                        $line[] = '';
                    }
                }
                fputcsv($handle, $line);
                $total++;
            }
        });

        fclose($handle);

        $this->info('Exported ' . number_format($total) . ' rows.');
        $this->info('File: ' . $fullPath);
        $this->line('');
        $this->line('Open the path above in your project to find the CSV file.');

        return 0;
    }
}
