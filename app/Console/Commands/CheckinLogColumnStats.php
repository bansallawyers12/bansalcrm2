<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckinLogColumnStats extends Command
{
    protected $signature = 'checkin-log:column-stats {--connection= : Database connection (default from config)}';

    protected $description = 'Report non-null counts per column in checkin_logs (PostgreSQL)';

    public function handle()
    {
        $connection = $this->option('connection') ?? config('database.default');

        try {
            $row = DB::connection($connection)
                ->table('checkin_logs')
                ->selectRaw("
                    count(*) AS total_rows,
                    count(id) AS id,
                    count(client_id) AS client_id,
                    count(user_id) AS user_id,
                    count(visit_purpose) AS visit_purpose,
                    count(office) AS office,
                    count(contact_type) AS contact_type,
                    count(status) AS status,
                    count(date) AS date,
                    count(sesion_start) AS sesion_start,
                    count(sesion_end) AS sesion_end,
                    count(wait_time) AS wait_time,
                    count(attend_time) AS attend_time,
                    count(wait_type) AS wait_type,
                    count(is_archived) AS is_archived,
                    count(created_at) AS created_at,
                    count(updated_at) AS updated_at
                ")
                ->first();

            if (!$row || (int) $row->total_rows === 0) {
                $this->warn('Table checkin_logs is empty or does not exist.');
                return 0;
            }

            $total = (int) $row->total_rows;
            $headers = ['Column', 'Non-null count', 'Null count', 'Fill %'];
            $rows = [];

            foreach ((array) $row as $column => $value) {
                if ($column === 'total_rows') {
                    continue;
                }
                $count = (int) $value;
                $nulls = $total - $count;
                $pct = $total > 0 ? round(100 * $count / $total, 1) : 0;
                $rows[] = [$column, $count, $nulls, $pct . '%'];
            }

            $this->info("checkin_logs column stats (connection: {$connection}, total rows: {$total})");
            $this->table($headers, $rows);

            return 0;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }
}
