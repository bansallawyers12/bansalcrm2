<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;

class TransferAllTablesSequentially extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:transfer-all {--batch=1000 : Batch size for inserts}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transfer all tables from MySQL to PostgreSQL sequentially, updating status in markdown file';

    /**
     * Tables to skip
     */
    private $skipTables = [
        'activities_logs',
        'followups',
        'mail_reports',
        'notes',
        'application_activities_logs',
    ];

    /**
     * Markdown file path
     */
    private $markdownFile;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->markdownFile = base_path('postgres_tables_list.md');
        
        $this->info('Starting sequential data transfer from MySQL to PostgreSQL...');
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

            // Read tables from markdown file
            $tables = $this->readTablesFromMarkdown();
            
            if (empty($tables)) {
                $this->error('No tables found in markdown file.');
                return 1;
            }

            $this->info('Found ' . count($tables) . ' tables to process.');
            $this->info('Skipping: ' . implode(', ', $this->skipTables));
            $this->newLine();

            // Mark skipped tables in markdown file
            foreach ($tables as $table) {
                $tableName = trim($table['name'], '`');
                if (in_array($tableName, $this->skipTables)) {
                    $this->updateMarkdownStatus($table['line'], $tableName, 'SKIPPED', 'Table in skip list');
                }
            }

            // Get table sizes and sort by size (ascending)
            $this->info('Checking table sizes...');
            $tablesWithSizes = [];
            foreach ($tables as $table) {
                $tableName = trim($table['name'], '`');
                
                // Skip specified tables
                if (in_array($tableName, $this->skipTables)) {
                    continue;
                }

                // Skip if already marked as COMPLETE
                if (isset($table['status']) && $table['status'] === 'COMPLETE') {
                    continue;
                }

                try {
                    $size = $this->getTableSize($tableName, $mysqlConnection);
                    $tablesWithSizes[] = [
                        'table' => $table,
                        'size' => $size,
                        'name' => $tableName
                    ];
                } catch (\Exception $e) {
                    // If we can't get size, set to 0 and continue
                    $tablesWithSizes[] = [
                        'table' => $table,
                        'size' => 0,
                        'name' => $tableName
                    ];
                }
            }

            // Sort by size (ascending)
            usort($tablesWithSizes, function($a, $b) {
                return $a['size'] <=> $b['size'];
            });

            // Limit to first 45 smallest tables
            $tablesWithSizes = array_slice($tablesWithSizes, 0, 45);

            $this->info('Processing first 45 smallest tables (sorted by size):');
            foreach ($tablesWithSizes as $item) {
                $this->line("  - {$item['name']}: " . number_format($item['size']) . " records");
            }
            $this->newLine();

            $batchSize = (int) $this->option('batch');
            $totalTransferred = 0;
            $totalSkipped = 0;
            $errors = [];

            foreach ($tablesWithSizes as $index => $item) {
                $table = $item['table'];
                $tableName = $item['name'];

                $this->info("[" . ($index + 1) . "/" . count($tablesWithSizes) . "] Processing: {$tableName} (" . number_format($item['size']) . " records)");
                
                try {
                    // Delete existing data from PostgreSQL table
                    $this->info("  Deleting existing data from PostgreSQL...");
                    $this->deleteTableData($tableName, $pgsqlConnection);
                    
                    $result = $this->transferTable($tableName, $mysqlConnection, $pgsqlConnection, $batchSize);
                    
                    if ($result['success']) {
                        $this->info("  ✓ Transferred: {$result['transferred']} records");
                        if ($result['skipped'] > 0) {
                            $this->warn("  ⚠ Skipped: {$result['skipped']} records (errors during insert)");
                        }
                        $totalTransferred += $result['transferred'];
                        $totalSkipped += $result['skipped'];
                        $this->updateMarkdownStatus($table['line'], $tableName, 'COMPLETE', "Transferred: {$result['transferred']}" . ($result['skipped'] > 0 ? ", Errors: {$result['skipped']}" : ""));
                    } else {
                        $errorMsg = $result['error'];
                        $this->error("  ✗ Error: {$errorMsg}");
                        $errors[$tableName] = $errorMsg;
                        $this->updateMarkdownStatus($table['line'], $tableName, 'ERROR', $errorMsg);
                    }
                } catch (\Exception $e) {
                    $errorMsg = $e->getMessage();
                    $this->error("  ✗ Exception: {$errorMsg}");
                    $errors[$tableName] = $errorMsg;
                    $this->updateMarkdownStatus($table['line'], $tableName, 'ERROR', $errorMsg);
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
     * Read tables from markdown file
     *
     * @return array
     */
    private function readTablesFromMarkdown()
    {
        if (!File::exists($this->markdownFile)) {
            $this->error("Markdown file not found: {$this->markdownFile}");
            return [];
        }

        $content = File::get($this->markdownFile);
        $lines = explode("\n", $content);
        $tables = [];

        foreach ($lines as $lineNum => $line) {
            // Match lines like: 1. `table_name` or 1. `table_name` - Status
            if (preg_match('/^\d+\.\s+`([^`]+)`/', $line, $matches)) {
                $status = null;
                // Check if line has a status
                if (preg_match('/- (✓|✗|⊘)\s*(COMPLETE|ERROR|SKIPPED)/', $line, $statusMatches)) {
                    $status = $statusMatches[2];
                }
                
                $tables[] = [
                    'name' => $matches[1],
                    'line' => $lineNum + 1,
                    'original' => $line,
                    'status' => $status
                ];
            }
        }

        return $tables;
    }

    /**
     * Update markdown file with status
     *
     * @param int $lineNumber
     * @param string $tableName
     * @param string $status
     * @param string $message
     * @return void
     */
    private function updateMarkdownStatus($lineNumber, $tableName, $status, $message = '')
    {
        try {
            $content = File::get($this->markdownFile);
            $lines = explode("\n", $content);

            if (!isset($lines[$lineNumber - 1])) {
                return;
            }

            $originalLine = $lines[$lineNumber - 1];
            
            // Remove existing status if any
            $cleanLine = preg_replace('/\s*-\s*(COMPLETE|ERROR|SKIPPED).*$/', '', $originalLine);
            $cleanLine = rtrim($cleanLine);

            // Add status
            $statusSymbol = '';
            switch ($status) {
                case 'COMPLETE':
                    $statusSymbol = '✓';
                    break;
                case 'ERROR':
                    $statusSymbol = '✗';
                    break;
                case 'SKIPPED':
                    $statusSymbol = '⊘';
                    break;
            }

            $newLine = $cleanLine . " - {$statusSymbol} {$status}";
            if ($message) {
                $newLine .= " ({$message})";
            }

            $lines[$lineNumber - 1] = $newLine;
            File::put($this->markdownFile, implode("\n", $lines));
        } catch (\Exception $e) {
            $this->warn("Could not update markdown file: " . $e->getMessage());
        }
    }

    /**
     * Transfer data for a single table
     *
     * @param string $table
     * @param \Illuminate\Database\Connection $mysqlConnection
     * @param \Illuminate\Database\Connection $pgsqlConnection
     * @param int $batchSize
     * @return array
     */
    private function transferTable($table, $mysqlConnection, $pgsqlConnection, $batchSize)
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

        $transferred = 0;
        $skipped = 0;
        $processed = 0;

        // Process records in chunks to avoid memory issues
        $mysqlConnection->table($table)
            ->orderBy($primaryKey)
            ->chunk($batchSize, function ($chunk) use ($table, $primaryKey, $pgsqlConnection, &$transferred, &$skipped, &$processed, $totalRecords, $pgsqlColumns) {
                $batch = [];

                foreach ($chunk as $record) {
                    $processed++;
                    $recordArray = (array) $record;

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
                                    $this->warn("    Failed to insert record: " . $e2->getMessage());
                                }
                        }
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

    /**
     * Get table size (row count) from MySQL
     *
     * @param string $table
     * @param \Illuminate\Database\Connection $connection
     * @return int
     */
    private function getTableSize($table, $connection)
    {
        try {
            if (!$this->tableExists($table, 'mysql')) {
                return 0;
            }
            return $connection->table($table)->count();
        } catch (\Exception $e) {
            return 0;
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
            if (!$this->tableExists($table, 'pgsql')) {
                return;
            }
            
            // Get count before deletion
            $count = $connection->table($table)->count();
            
            if ($count > 0) {
                // Use TRUNCATE for better performance in PostgreSQL
                // If TRUNCATE fails (due to foreign keys), fall back to DELETE
                try {
                    $connection->statement("TRUNCATE TABLE \"{$table}\" RESTART IDENTITY CASCADE");
                    $this->line("    Truncated {$count} existing records");
                } catch (\Exception $e) {
                    // If TRUNCATE fails, try DELETE
                    try {
                        $deletedCount = $connection->table($table)->whereRaw('1=1')->delete();
                        $this->line("    Deleted {$deletedCount} existing records");
                    } catch (\Exception $e2) {
                        $this->warn("    Warning: Could not delete existing data: " . $e2->getMessage());
                    }
                }
            } else {
                $this->line("    Table is already empty");
            }
        } catch (\Exception $e) {
            $this->warn("    Warning: Could not delete existing data: " . $e->getMessage());
        }
    }
}
