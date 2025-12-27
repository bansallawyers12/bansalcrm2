<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TransferDataToPostgres extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:transfer {--table= : Transfer specific table only} {--batch=1000 : Batch size for inserts} {--dry-run : Show what would be transferred without actually transferring}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transfer data from MySQL to PostgreSQL database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting data transfer from MySQL to PostgreSQL...');
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

            $isDryRun = $this->option('dry-run');
            $batchSize = (int) $this->option('batch');

            // Define tables to transfer in order (smallest first)
            $tablesToTransfer = [
                'tasks' => 34,
                'agents' => 146,
                'partners' => 508,
                'account_client_receipts' => 2689,
                'invoice_payments' => 4805,
                'leads' => 9802,
                'interested_services' => 15087,
                'applications' => 27797,
                'admins' => 53095,
            ];

            $tableName = $this->option('table');
            if ($tableName) {
                if (!isset($tablesToTransfer[$tableName])) {
                    $this->error("Table '{$tableName}' is not in the list of tables to transfer.");
                    $this->info('Available tables: ' . implode(', ', array_keys($tablesToTransfer)));
                    return 1;
                }
                $tablesToTransfer = [$tableName => $tablesToTransfer[$tableName]];
            }

            if ($isDryRun) {
                $this->warn('DRY RUN MODE - No data will be transferred');
                $this->newLine();
            }

            $totalTransferred = 0;
            $totalSkipped = 0;
            $errors = [];

            foreach ($tablesToTransfer as $table => $expectedCount) {
                $this->info("Processing table: {$table}");
                
                try {
                    $result = $this->transferTable($table, $mysqlConnection, $pgsqlConnection, $batchSize, $isDryRun);
                    
                    if ($result['success']) {
                        $this->info("  ✓ Transferred: {$result['transferred']} records");
                        if ($result['skipped'] > 0) {
                            $this->warn("  ⚠ Skipped: {$result['skipped']} records (already exist)");
                        }
                        $totalTransferred += $result['transferred'];
                        $totalSkipped += $result['skipped'];
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
            $this->info('=== TRANSFER SUMMARY ===');
            $this->info("Total records transferred: " . number_format($totalTransferred));
            if ($totalSkipped > 0) {
                $this->info("Total records skipped: " . number_format($totalSkipped));
            }
            if (count($errors) > 0) {
                $this->error("Tables with errors: " . count($errors));
                foreach ($errors as $table => $error) {
                    $this->error("  - {$table}: {$error}");
                }
            }

            if ($isDryRun) {
                $this->newLine();
                $this->warn('This was a DRY RUN - No data was actually transferred');
            }

            $this->newLine();
            $this->info('Transfer completed!');
            
            return 0;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }
    }

    /**
     * Transfer data for a single table
     *
     * @param string $table
     * @param \Illuminate\Database\Connection $mysqlConnection
     * @param \Illuminate\Database\Connection $pgsqlConnection
     * @param int $batchSize
     * @param bool $isDryRun
     * @return array
     */
    private function transferTable($table, $mysqlConnection, $pgsqlConnection, $batchSize, $isDryRun)
    {
        // Check if table exists in MySQL
        if (!$this->tableExists($table, 'mysql')) {
            return [
                'success' => false,
                'error' => 'Table does not exist in MySQL',
                'transferred' => 0,
                'skipped' => 0,
            ];
        }

        // Check if table exists in PostgreSQL
        if (!$this->tableExists($table, 'pgsql')) {
            return [
                'success' => false,
                'error' => 'Table does not exist in PostgreSQL',
                'transferred' => 0,
                'skipped' => 0,
            ];
        }

        // Get primary key
        $primaryKey = $this->getPrimaryKey($table, 'mysql');
        if (!$primaryKey) {
            return [
                'success' => false,
                'error' => 'Could not determine primary key',
                'transferred' => 0,
                'skipped' => 0,
            ];
        }

        // Get PostgreSQL columns to filter out non-existent columns
        $pgsqlColumns = $this->getTableColumns($table, 'pgsql');
        if (empty($pgsqlColumns)) {
            return [
                'success' => false,
                'error' => 'Could not get PostgreSQL table columns',
                'transferred' => 0,
                'skipped' => 0,
            ];
        }

        // Get total count for progress tracking
        $totalRecords = $mysqlConnection->table($table)->count();
        
        if ($totalRecords === 0) {
            return [
                'success' => true,
                'transferred' => 0,
                'skipped' => 0,
            ];
        }

        // Get existing IDs in PostgreSQL (chunked for large tables)
        $existingIds = [];
        if (!$isDryRun) {
            try {
                $pgsqlConnection->table($table)
                    ->select($primaryKey)
                    ->chunk(10000, function ($chunk) use (&$existingIds, $primaryKey) {
                        foreach ($chunk as $row) {
                            $existingIds[$row->$primaryKey] = true;
                        }
                    });
            } catch (\Exception $e) {
                // Table might be empty, continue
            }
        }

        $transferred = 0;
        $skipped = 0;
        $processed = 0;

        // Process records in chunks to avoid memory issues
        $mysqlConnection->table($table)
            ->orderBy($primaryKey)
            ->chunk($batchSize, function ($chunk) use ($table, $primaryKey, $pgsqlConnection, $isDryRun, &$transferred, &$skipped, &$processed, $existingIds, $totalRecords, $pgsqlColumns) {
                $batch = [];

                foreach ($chunk as $record) {
                    $processed++;
                    $recordArray = (array) $record;
                    $recordId = $recordArray[$primaryKey];

                    // Check if record already exists
                    if (!$isDryRun && isset($existingIds[$recordId])) {
                        $skipped++;
                        continue;
                    }

                    // Filter to only include columns that exist in PostgreSQL
                    $filteredRecord = [];
                    foreach ($recordArray as $key => $value) {
                        if (in_array($key, $pgsqlColumns)) {
                            $filteredRecord[$key] = $value;
                        }
                    }

                    $batch[] = $filteredRecord;
                }

                // Insert batch
                if (count($batch) > 0) {
                    if (!$isDryRun) {
                        try {
                            $this->insertBatch($table, $batch, $pgsqlConnection);
                            $transferred += count($batch);
                        } catch (\Exception $e) {
                            // Try inserting one by one if batch fails
                            foreach ($batch as $singleRecord) {
                                try {
                                    $this->insertRecord($table, $singleRecord, $pgsqlConnection);
                                    $transferred++;
                                } catch (\Exception $e2) {
                                    $skipped++;
                                    // Log error but continue
                                    $this->warn("    Failed to insert record ID {$singleRecord[$primaryKey]}: " . $e2->getMessage());
                                }
                            }
                        }
                    } else {
                        $transferred += count($batch);
                    }
                }

                // Show progress for large tables
                if ($totalRecords > 1000 && $processed % 5000 == 0) {
                    $this->line("    Progress: {$processed}/{$totalRecords} records processed");
                }
            });

        return [
            'success' => true,
            'transferred' => $transferred,
            'skipped' => $skipped,
        ];
    }

    /**
     * Insert a batch of records
     *
     * @param string $table
     * @param array $batch
     * @param \Illuminate\Database\Connection $connection
     * @return void
     */
    private function insertBatch($table, $batch, $connection)
    {
        // Clean the data - handle special cases
        $cleanedBatch = [];
        foreach ($batch as $record) {
            $cleanedRecord = $this->cleanRecord($record);
            $cleanedBatch[] = $cleanedRecord;
        }

        $connection->table($table)->insert($cleanedBatch);
    }

    /**
     * Insert a single record
     *
     * @param string $table
     * @param array $record
     * @param \Illuminate\Database\Connection $connection
     * @return void
     */
    private function insertRecord($table, $record, $connection)
    {
        $cleanedRecord = $this->cleanRecord($record);
        $connection->table($table)->insert($cleanedRecord);
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
            // These are required in PostgreSQL, so we must provide a value
            if ($key === 'created_at' || $key === 'updated_at') {
                // Check for null, empty, or invalid MySQL dates
                if ($value === null || 
                    $value === '' || 
                    (is_string($value) && trim($value) === '') ||
                    (is_string($value) && preg_match('/^0000-/', $value))) {
                    $cleaned[$key] = $currentTimestamp;
                    continue;
                }
                // If it's a valid date, use it
                $cleaned[$key] = $value;
                continue;
            }
            
            // Handle null values - but check for required date fields
            if ($value === null || $value === '') {
                // For required date fields in PostgreSQL, use a default date
                if (stripos($key, 'contract_expiry_date') !== false) {
                    $cleaned[$key] = '2099-12-31'; // Far future date as default
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

            // Handle dates - ensure proper format
            if ($value instanceof \DateTime || $value instanceof \Carbon\Carbon) {
                $cleaned[$key] = $value->format('Y-m-d H:i:s');
                continue;
            }

            // Handle invalid MySQL dates (0000-00-00, 0000-01-01, 0000-02-01, etc.)
            if (is_string($value)) {
                // Check for invalid MySQL date formats - any date starting with 0000-
                if (preg_match('/^0000-/', $value)) {
                    // For required timestamp fields, use current timestamp
                    if ($key === 'created_at' || $key === 'updated_at') {
                        $cleaned[$key] = $currentTimestamp;
                    } elseif (stripos($key, 'contract_expiry_date') !== false) {
                        $cleaned[$key] = '2099-12-31'; // Far future date as default
                    } else {
                        $cleaned[$key] = null;
                    }
                    continue;
                }
                
                // Check for empty date strings
                if (trim($value) === '' && (stripos($key, 'date') !== false || stripos($key, 'time') !== false)) {
                    // For required timestamp fields, use current timestamp
                    if ($key === 'created_at' || $key === 'updated_at') {
                        $cleaned[$key] = $currentTimestamp;
                    } elseif (stripos($key, 'contract_expiry_date') !== false) {
                        $cleaned[$key] = '2099-12-31'; // Far future date as default
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
            if (Schema::connection($connection)->hasColumn($table, 'id')) {
                return 'id';
            }

        } catch (\Exception $e) {
            // Try common primary key names
            $commonKeys = ['id', $table . '_id', 'uuid'];
            foreach ($commonKeys as $key) {
                try {
                    if (Schema::connection($connection)->hasColumn($table, $key)) {
                        return $key;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }
        }

        return null;
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
                $schemaColumns = Schema::connection($connection)->getColumnListing($table);
                $columns = $schemaColumns;
            } catch (\Exception $e2) {
                // Return empty array if we can't get columns
            }
        }
        
        return $columns;
    }
}

