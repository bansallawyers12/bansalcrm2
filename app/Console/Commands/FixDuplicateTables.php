<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixDuplicateTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:fix-duplicates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete existing data in PostgreSQL and transfer fresh data from MySQL for tables with duplicates';

    /**
     * Tables to fix
     */
    private $tablesToFix = [
        'client_phones',
        'client_service_takens',
        'invoice_details',
        'invoice_payments',
        'invoices',
        'partner_student_invoices',
        'products',
    ];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting fix for tables with duplicate data...');
        $this->newLine();

        try {
            // Test connections
            $this->info('Testing database connections...');
            
            $mysqlConnection = DB::connection('mysql');
            $pgsqlConnection = DB::connection('pgsql');
            
            $mysqlConnection->getPdo();
            $pgsqlConnection->getPdo();
            
            $this->info('✓ MySQL connection: OK');
            $this->info('✓ PostgreSQL connection: OK');
            $this->newLine();

            $totalTransferred = 0;
            $errors = [];

            foreach ($this->tablesToFix as $table) {
                $this->info("Processing table: {$table}");
                
                try {
                    // Check if table exists
                    if (!$this->tableExists($table, 'mysql')) {
                        $this->error("  ✗ Table does not exist in MySQL");
                        $errors[$table] = 'Table does not exist in MySQL';
                        continue;
                    }

                    if (!$this->tableExists($table, 'pgsql')) {
                        $this->error("  ✗ Table does not exist in PostgreSQL");
                        $errors[$table] = 'Table does not exist in PostgreSQL';
                        continue;
                    }

                    // Get counts before deletion
                    $mysqlCount = $mysqlConnection->table($table)->count();
                    $pgsqlCountBefore = $pgsqlConnection->table($table)->count();
                    
                    $this->line("  MySQL records: " . number_format($mysqlCount));
                    $this->line("  PostgreSQL records before: " . number_format($pgsqlCountBefore));

                    // Delete existing data from PostgreSQL
                    $this->line("  Deleting existing data from PostgreSQL...");
                    $this->deleteTableData($table, $pgsqlConnection);

                    // Transfer data from MySQL to PostgreSQL
                    $this->line("  Transferring data from MySQL to PostgreSQL...");
                    $result = $this->transferTable($table, $mysqlConnection, $pgsqlConnection);

                    if ($result['success']) {
                        $pgsqlCountAfter = $pgsqlConnection->table($table)->count();
                        $this->info("  ✓ Transferred: {$result['transferred']} records");
                        $this->info("  ✓ PostgreSQL records after: " . number_format($pgsqlCountAfter));
                        
                        if ($mysqlCount === $pgsqlCountAfter) {
                            $this->info("  ✓ Counts match!");
                        } else {
                            $this->warn("  ⚠ Count mismatch: MySQL has {$mysqlCount}, PostgreSQL has {$pgsqlCountAfter}");
                        }
                        
                        $totalTransferred += $result['transferred'];
                    } else {
                        $this->error("  ✗ Error: {$result['error']}");
                        $errors[$table] = $result['error'];
                    }
                } catch (\Exception $e) {
                    $this->error("  ✗ Exception: " . $e->getMessage());
                    $errors[$table] = $e->getMessage();
                }
                
                $this->newLine();
            }

            // Summary
            $this->info('=== SUMMARY ===');
            $this->info("Total records transferred: " . number_format($totalTransferred));
            if (count($errors) > 0) {
                $this->error("Tables with errors: " . count($errors));
                foreach ($errors as $table => $error) {
                    $this->error("  - {$table}: {$error}");
                }
            } else {
                $this->info("✓ All tables processed successfully!");
            }

            $this->newLine();
            $this->info('Fix completed!');
            
            return 0;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }
    }

    /**
     * Delete all existing data from PostgreSQL table
     *
     * @param string $table
     * @param \Illuminate\Database\Connection $connection
     * @return void
     */
    private function deleteTableData($table, $connection)
    {
        try {
            // Get count before deletion
            $count = $connection->table($table)->count();
            
            if ($count > 0) {
                // Use TRUNCATE for better performance in PostgreSQL
                try {
                    $connection->statement("TRUNCATE TABLE \"{$table}\" RESTART IDENTITY CASCADE");
                    $this->line("    Truncated {$count} existing records");
                } catch (\Exception $e) {
                    // If TRUNCATE fails, try DELETE
                    try {
                        $deletedCount = $connection->table($table)->whereRaw('1=1')->delete();
                        $this->line("    Deleted {$deletedCount} existing records");
                    } catch (\Exception $e2) {
                        throw new \Exception("Could not delete existing data: " . $e2->getMessage());
                    }
                }
            } else {
                $this->line("    Table is already empty");
            }
        } catch (\Exception $e) {
            throw new \Exception("Could not delete existing data: " . $e->getMessage());
        }
    }

    /**
     * Transfer data for a single table
     *
     * @param string $table
     * @param \Illuminate\Database\Connection $mysqlConnection
     * @param \Illuminate\Database\Connection $pgsqlConnection
     * @return array
     */
    private function transferTable($table, $mysqlConnection, $pgsqlConnection)
    {
        // Get PostgreSQL columns to filter out non-existent columns
        $pgsqlColumns = $this->getTableColumns($table, 'pgsql');
        if (empty($pgsqlColumns)) {
            return [
                'success' => false,
                'error' => 'Could not get PostgreSQL table columns',
                'transferred' => 0,
            ];
        }

        // Get total count for progress tracking
        $totalRecords = $mysqlConnection->table($table)->count();
        
        if ($totalRecords === 0) {
            return [
                'success' => true,
                'transferred' => 0,
            ];
        }

        $transferred = 0;
        $batchSize = 1000;

        // Get primary key for ordering
        $primaryKey = $this->getPrimaryKey($table, 'mysql');
        if (!$primaryKey) {
            // Fallback to 'id' or first column
            $primaryKey = 'id';
            if (!in_array('id', $pgsqlColumns)) {
                $primaryKey = !empty($pgsqlColumns) ? $pgsqlColumns[0] : 'id';
            }
        }

        // Process records in chunks to avoid memory issues
        $mysqlConnection->table($table)
            ->orderBy($primaryKey)
            ->chunk($batchSize, function ($chunk) use ($table, $pgsqlConnection, &$transferred, $pgsqlColumns) {
                $batch = [];

                foreach ($chunk as $record) {
                    $recordArray = (array) $record;

                    // Filter to only include columns that exist in PostgreSQL
                    $filteredRecord = [];
                    foreach ($recordArray as $key => $value) {
                        if (in_array($key, $pgsqlColumns)) {
                            $filteredRecord[$key] = $value;
                        }
                    }

                    $batch[] = $this->cleanRecord($filteredRecord);
                }

                // Insert batch
                if (count($batch) > 0) {
                    try {
                        $pgsqlConnection->table($table)->insert($batch);
                        $transferred += count($batch);
                    } catch (\Exception $e) {
                        // Try inserting one by one if batch fails
                        foreach ($batch as $singleRecord) {
                            try {
                                $pgsqlConnection->table($table)->insert($singleRecord);
                                $transferred++;
                            } catch (\Exception $e2) {
                                // Log error but continue
                                $this->warn("    Failed to insert record: " . $e2->getMessage());
                            }
                        }
                    }
                }
            });

        return [
            'success' => true,
            'transferred' => $transferred,
        ];
    }

    /**
     * Clean record data for PostgreSQL compatibility
     *
     * @param array $record
     * @return array
     */
    private function cleanRecord($record)
    {
        $cleaned = [];
        $currentTimestamp = date('Y-m-d H:i:s');
        
        foreach ($record as $key => $value) {
            // Handle required timestamp fields first (created_at, updated_at)
            if ($key === 'created_at' || $key === 'updated_at') {
                if ($value === null || 
                    $value === '' || 
                    (is_string($value) && trim($value) === '') ||
                    (is_string($value) && preg_match('/^0000-/', $value))) {
                    $cleaned[$key] = $currentTimestamp;
                    continue;
                }
                $cleaned[$key] = $value;
                continue;
            }
            
            // Handle null values
            if ($value === null || $value === '') {
                if (stripos($key, 'contract_expiry_date') !== false) {
                    $cleaned[$key] = '2099-12-31';
                    continue;
                }
                $cleaned[$key] = null;
                continue;
            }

            // Handle boolean values
            if (is_bool($value)) {
                $cleaned[$key] = $value ? 1 : 0;
                continue;
            }

            // Handle arrays/objects (convert to JSON)
            if (is_array($value) || is_object($value)) {
                $cleaned[$key] = json_encode($value);
                continue;
            }

            // Handle dates
            if ($value instanceof \DateTime || $value instanceof \Carbon\Carbon) {
                $cleaned[$key] = $value->format('Y-m-d H:i:s');
                continue;
            }

            // Handle invalid MySQL dates (0000-00-00, etc.)
            if (is_string($value)) {
                if (preg_match('/^0000-/', $value)) {
                    if ($key === 'created_at' || $key === 'updated_at') {
                        $cleaned[$key] = $currentTimestamp;
                    } elseif (stripos($key, 'contract_expiry_date') !== false) {
                        $cleaned[$key] = '2099-12-31';
                    } else {
                        $cleaned[$key] = null;
                    }
                    continue;
                }
                
                if (trim($value) === '' && (stripos($key, 'date') !== false || stripos($key, 'time') !== false)) {
                    if ($key === 'created_at' || $key === 'updated_at') {
                        $cleaned[$key] = $currentTimestamp;
                    } elseif (stripos($key, 'contract_expiry_date') !== false) {
                        $cleaned[$key] = '2099-12-31';
                    } else {
                        $cleaned[$key] = null;
                    }
                    continue;
                }
            }

            // Everything else as-is
            $cleaned[$key] = $value;
        }

        return $cleaned;
    }

    /**
     * Check if table exists in database
     *
     * @param string $table
     * @param string $connection
     * @return bool
     */
    private function tableExists($table, $connection)
    {
        try {
            if ($connection === 'mysql') {
                $database = config("database.connections.mysql.database");
                $result = DB::connection('mysql')->select(
                    "SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = ? AND table_name = ?",
                    [$database, $table]
                );
                return $result[0]->count > 0;
            } elseif ($connection === 'pgsql') {
                $result = DB::connection('pgsql')->select(
                    "SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = 'public' AND table_name = ?",
                    [$table]
                );
                return $result[0]->count > 0;
            }
        } catch (\Exception $e) {
            return false;
        }
        
        return false;
    }

    /**
     * Get table columns for a database connection
     *
     * @param string $table
     * @param string $connection
     * @return array
     */
    private function getTableColumns($table, $connection)
    {
        $columns = [];
        
        try {
            if ($connection === 'mysql') {
                $database = config("database.connections.mysql.database");
                $result = DB::connection('mysql')->select("
                    SELECT COLUMN_NAME
                    FROM INFORMATION_SCHEMA.COLUMNS
                    WHERE TABLE_SCHEMA = ?
                    AND TABLE_NAME = ?
                    ORDER BY ORDINAL_POSITION
                ", [$database, $table]);
                
                foreach ($result as $row) {
                    $columns[] = $row->COLUMN_NAME;
                }
            } elseif ($connection === 'pgsql') {
                $result = DB::connection('pgsql')->select("
                    SELECT column_name
                    FROM information_schema.columns
                    WHERE table_schema = 'public'
                    AND table_name = ?
                    ORDER BY ordinal_position
                ", [$table]);
                
                foreach ($result as $row) {
                    $columns[] = $row->column_name;
                }
            }
        } catch (\Exception $e) {
            // If we can't get columns, try using Schema facade
            try {
                $schemaColumns = \Illuminate\Support\Facades\Schema::connection($connection)->getColumnListing($table);
                $columns = $schemaColumns;
            } catch (\Exception $e2) {
                // Return empty array if we can't get columns
            }
        }
        
        return $columns;
    }

    /**
     * Get primary key column name for a table
     *
     * @param string $table
     * @param string $connection
     * @return string|null
     */
    private function getPrimaryKey($table, $connection)
    {
        try {
            if ($connection === 'mysql') {
                $database = config("database.connections.mysql.database");
                $result = DB::connection('mysql')->select("
                    SELECT COLUMN_NAME
                    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                    WHERE TABLE_SCHEMA = ?
                    AND TABLE_NAME = ?
                    AND CONSTRAINT_NAME = 'PRIMARY'
                    LIMIT 1
                ", [$database, $table]);

                if (!empty($result)) {
                    return $result[0]->COLUMN_NAME;
                }
            } elseif ($connection === 'pgsql') {
                $result = DB::connection('pgsql')->select("
                    SELECT a.attname
                    FROM pg_index i
                    JOIN pg_attribute a ON a.attrelid = i.indrelid AND a.attnum = ANY(i.indkey)
                    WHERE i.indrelid = ?::regclass
                    AND i.indisprimary
                    LIMIT 1
                ", [$table]);

                if (!empty($result) && isset($result[0]->attname)) {
                    return $result[0]->attname;
                }
            }

            // Fallback: try 'id' column
            if (\Illuminate\Support\Facades\Schema::connection($connection)->hasColumn($table, 'id')) {
                return 'id';
            }

        } catch (\Exception $e) {
            // Try common primary key names
            $commonKeys = ['id', $table . '_id', 'uuid'];
            foreach ($commonKeys as $key) {
                try {
                    if (\Illuminate\Support\Facades\Schema::connection($connection)->hasColumn($table, $key)) {
                        return $key;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }
        }

        return null;
    }
}
