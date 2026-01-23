<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;

class CompareDatabases extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:compare {--table= : Compare specific table only} {--detailed : Show detailed missing records} {--from-file : Read tables from postgres_tables_list.md} {--count-only : Only compare row counts, skip data content comparison}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compare data between MySQL and PostgreSQL databases and identify missing records';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting database comparison...');
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

            // Get tables to compare
            $tableName = $this->option('table');
            
            if ($this->option('from-file') || $tableName) {
                // Read from markdown file if --from-file is set, or if specific table is requested
                $tables = $tableName ? [$tableName] : $this->readTablesFromMarkdown();
                if (empty($tables) && $this->option('from-file')) {
                    $this->warn('No tables found in markdown file, falling back to all MySQL tables...');
                    $tables = $this->getAllTables('mysql');
                }
            } else {
                $tables = $tableName ? [$tableName] : $this->getAllTables('mysql');
            }

            if (empty($tables)) {
                $this->error('No tables found in MySQL database.');
                return 1;
            }

            $this->info('Found ' . count($tables) . ' tables to compare.');
            $this->newLine();

            $results = [];
            $totalMissing = 0;

            $progressBar = $this->output->createProgressBar(count($tables));
            $progressBar->start();

            foreach ($tables as $table) {
                try {
                    $comparison = $this->compareTable($table, $mysqlConnection, $pgsqlConnection);
                    $results[$table] = $comparison;
                    
                    if ($comparison['mysql_count'] !== $comparison['pgsql_count']) {
                        $totalMissing += abs($comparison['mysql_count'] - $comparison['pgsql_count']);
                    }
                } catch (\Exception $e) {
                    $results[$table] = [
                        'error' => $e->getMessage(),
                        'mysql_count' => 0,
                        'pgsql_count' => 0,
                    ];
                }
                
                $progressBar->advance();
            }

            $progressBar->finish();
            $this->newLine(2);

            // Display results
            $this->displayResults($results);

            // Generate detailed report if requested
            if ($this->option('detailed')) {
                $this->generateDetailedReport($results, $mysqlConnection, $pgsqlConnection);
            }

            $this->newLine();
            $this->info('Comparison completed!');
            
            return 0;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }
    }

    /**
     * Get all tables from a database connection
     *
     * @param string $connection
     * @return array
     */
    private function getAllTables($connection)
    {
        $tables = [];
        
        if ($connection === 'mysql') {
            $database = config("database.connections.mysql.database");
            $result = DB::connection('mysql')->select("SHOW TABLES");
            $key = "Tables_in_{$database}";
            
            foreach ($result as $row) {
                $tables[] = $row->$key;
            }
        } elseif ($connection === 'pgsql') {
            $result = DB::connection('pgsql')->select("
                SELECT table_name 
                FROM information_schema.tables 
                WHERE table_schema = 'public' 
                AND table_type = 'BASE TABLE'
                ORDER BY table_name
            ");
            
            foreach ($result as $row) {
                $tables[] = $row->table_name;
            }
        }
        
        return $tables;
    }

    /**
     * Compare a single table between MySQL and PostgreSQL
     *
     * @param string $table
     * @param \Illuminate\Database\Connection $mysqlConnection
     * @param \Illuminate\Database\Connection $pgsqlConnection
     * @return array
     */
    private function compareTable($table, $mysqlConnection, $pgsqlConnection)
    {
        $result = [
            'table' => $table,
            'mysql_count' => 0,
            'pgsql_count' => 0,
            'difference' => 0,
            'status' => 'unknown',
            'data_match' => null,
        ];

        try {
            // Get MySQL count
            $mysqlCount = $mysqlConnection->table($table)->count();
            $result['mysql_count'] = $mysqlCount;

            // Check if table exists in PostgreSQL
            $tableExists = $this->tableExists($table, 'pgsql');
            
            if (!$tableExists) {
                $result['pgsql_count'] = 0;
                $result['difference'] = $mysqlCount;
                $result['status'] = 'missing_table';
                $result['data_match'] = false;
                return $result;
            }

            // Get PostgreSQL count
            $pgsqlCount = $pgsqlConnection->table($table)->count();
            $result['pgsql_count'] = $pgsqlCount;
            $result['difference'] = $mysqlCount - $pgsqlCount;

            // If counts match, check if data content is the same (unless count-only mode)
            if ($mysqlCount === $pgsqlCount && $mysqlCount > 0) {
                if ($this->option('count-only')) {
                    $result['data_match'] = null; // Not checked
                    $result['status'] = 'match';
                } else {
                    $result['data_match'] = $this->compareDataContent($table, $mysqlConnection, $pgsqlConnection);
                    $result['status'] = $result['data_match'] ? 'match' : 'data_different';
                }
            } elseif ($mysqlCount === $pgsqlCount && $mysqlCount === 0) {
                $result['status'] = 'match';
                $result['data_match'] = true; // Both empty
            } elseif ($mysqlCount > $pgsqlCount) {
                $result['status'] = 'missing_in_pgsql';
                $result['data_match'] = false;
            } else {
                $result['status'] = 'extra_in_pgsql';
                $result['data_match'] = false;
            }

        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            $result['status'] = 'error';
            $result['data_match'] = false;
        }

        return $result;
    }

    /**
     * Compare data content between MySQL and PostgreSQL tables
     *
     * @param string $table
     * @param \Illuminate\Database\Connection $mysqlConnection
     * @param \Illuminate\Database\Connection $pgsqlConnection
     * @return bool
     */
    private function compareDataContent($table, $mysqlConnection, $pgsqlConnection)
    {
        try {
            $rowCount = $mysqlConnection->table($table)->count();
            
            // For very large tables (>10k rows), use hash-based comparison
            if ($rowCount > 10000) {
                return $this->compareDataByHash($table, $mysqlConnection, $pgsqlConnection);
            }
            
            // For medium tables (1k-10k), use sample comparison
            if ($rowCount > 1000) {
                return $this->compareDataBySample($table, $mysqlConnection, $pgsqlConnection, 100);
            }

            // For small tables, do full comparison
            // Get primary key
            $primaryKey = $this->getPrimaryKey($table, 'mysql');
            if (!$primaryKey) {
                // If no primary key, compare by all columns (sample-based)
                return $this->compareDataBySample($table, $mysqlConnection, $pgsqlConnection, 100);
            }

            // Get common columns
            $mysqlColumns = $this->getTableColumns($table, 'mysql');
            $pgsqlColumns = $this->getTableColumns($table, 'pgsql');
            $commonColumns = array_intersect($mysqlColumns, $pgsqlColumns);
            
            if (empty($commonColumns)) {
                return false;
            }

            // Compare records by primary key in chunks to avoid memory issues
            $chunkSize = 500;
            $allMatch = true;
            
            $mysqlConnection->table($table)
                ->select($commonColumns)
                ->orderBy($primaryKey)
                ->chunk($chunkSize, function ($mysqlChunk) use ($table, $primaryKey, $pgsqlConnection, $commonColumns, &$allMatch) {
                    if (!$allMatch) {
                        return false; // Stop processing if mismatch found
                    }
                    
                    $ids = $mysqlChunk->pluck($primaryKey)->toArray();
                    $pgsqlChunk = $pgsqlConnection->table($table)
                        ->select($commonColumns)
                        ->whereIn($primaryKey, $ids)
                        ->orderBy($primaryKey)
                        ->get()
                        ->keyBy($primaryKey);

                    // Compare each record
                    foreach ($mysqlChunk as $mysqlRecord) {
                        $key = $mysqlRecord->$primaryKey;
                        if (!isset($pgsqlChunk[$key])) {
                            $allMatch = false;
                            return false;
                        }

                        $mysqlData = $this->normalizeRecord((array)$mysqlRecord);
                        $pgsqlData = $this->normalizeRecord((array)$pgsqlChunk[$key]);

                        if ($mysqlData !== $pgsqlData) {
                            $allMatch = false;
                            return false;
                        }
                    }
                });

            return $allMatch;
        } catch (\Exception $e) {
            // If comparison fails, assume data is different
            return false;
        }
    }

    /**
     * Compare data using hash-based approach (for large tables)
     *
     * @param string $table
     * @param \Illuminate\Database\Connection $mysqlConnection
     * @param \Illuminate\Database\Connection $pgsqlConnection
     * @return bool
     */
    private function compareDataByHash($table, $mysqlConnection, $pgsqlConnection)
    {
        try {
            // Get common columns
            $mysqlColumns = $this->getTableColumns($table, 'mysql');
            $pgsqlColumns = $this->getTableColumns($table, 'pgsql');
            $commonColumns = array_intersect($mysqlColumns, $pgsqlColumns);
            
            if (empty($commonColumns)) {
                return false;
            }

            // Calculate hash of all data in chunks
            $mysqlHash = '';
            $pgsqlHash = '';
            
            // MySQL hash
            $mysqlConnection->table($table)
                ->select($commonColumns)
                ->orderBy($commonColumns[0])
                ->chunk(1000, function ($chunk) use (&$mysqlHash) {
                    foreach ($chunk as $record) {
                        $normalized = $this->normalizeRecord((array)$record);
                        $mysqlHash .= md5(json_encode($normalized));
                    }
                });

            // PostgreSQL hash
            $pgsqlConnection->table($table)
                ->select($commonColumns)
                ->orderBy($commonColumns[0])
                ->chunk(1000, function ($chunk) use (&$pgsqlHash) {
                    foreach ($chunk as $record) {
                        $normalized = $this->normalizeRecord((array)$record);
                        $pgsqlHash .= md5(json_encode($normalized));
                    }
                });

            return md5($mysqlHash) === md5($pgsqlHash);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Compare data by sampling records (for tables without primary key or medium tables)
     *
     * @param string $table
     * @param \Illuminate\Database\Connection $mysqlConnection
     * @param \Illuminate\Database\Connection $pgsqlConnection
     * @param int $sampleSize
     * @return bool
     */
    private function compareDataBySample($table, $mysqlConnection, $pgsqlConnection, $sampleSize = 100)
    {
        try {
            // Get common columns
            $mysqlColumns = $this->getTableColumns($table, 'mysql');
            $pgsqlColumns = $this->getTableColumns($table, 'pgsql');
            $commonColumns = array_intersect($mysqlColumns, $pgsqlColumns);
            
            if (empty($commonColumns)) {
                return false;
            }

            // Sample records from each database
            $mysqlSample = $mysqlConnection->table($table)
                ->select($commonColumns)
                ->limit($sampleSize)
                ->get();
            
            $pgsqlSample = $pgsqlConnection->table($table)
                ->select($commonColumns)
                ->limit($sampleSize)
                ->get();

            if ($mysqlSample->count() !== $pgsqlSample->count()) {
                return false;
            }

            // Compare by hashing normalized records
            $mysqlHashes = $mysqlSample->map(function($record) {
                return md5(json_encode($this->normalizeRecord((array)$record)));
            })->sort()->values();

            $pgsqlHashes = $pgsqlSample->map(function($record) {
                return md5(json_encode($this->normalizeRecord((array)$record)));
            })->sort()->values();

            return $mysqlHashes->toArray() === $pgsqlHashes->toArray();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Normalize record data for comparison (handle nulls, dates, etc.)
     *
     * @param array $record
     * @return array
     */
    private function normalizeRecord(array $record)
    {
        $normalized = [];
        
        foreach ($record as $key => $value) {
            // Convert null/empty strings to null
            if ($value === null || $value === '' || (is_string($value) && trim($value) === '')) {
                $normalized[$key] = null;
            }
            // Normalize dates/timestamps
            elseif (is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}/', $value)) {
                // Try to normalize date format
                try {
                    $date = new \DateTime($value);
                    $normalized[$key] = $date->format('Y-m-d H:i:s');
                } catch (\Exception $e) {
                    $normalized[$key] = $value;
                }
            }
            // Handle boolean values
            elseif (is_bool($value)) {
                $normalized[$key] = $value ? 1 : 0;
            }
            // Everything else as-is
            else {
                $normalized[$key] = $value;
            }
        }
        
        ksort($normalized); // Sort keys for consistent comparison
        return $normalized;
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
     * Display comparison results
     *
     * @param array $results
     * @return void
     */
    private function displayResults($results)
    {
        $this->info('=== COMPARISON RESULTS ===');
        $this->newLine();

        $headers = ['Table', 'MySQL Count', 'PostgreSQL Count', 'Difference', 'Data Match', 'Status'];
        $rows = [];

        $missingTables = [];
        $missingData = [];
        $dataDifferent = [];
        $matches = [];
        $errors = [];

        foreach ($results as $table => $result) {
            if (isset($result['error'])) {
                $rows[] = [
                    $table,
                    'N/A',
                    'N/A',
                    'N/A',
                    'N/A',
                    'ERROR: ' . $result['error']
                ];
                $errors[] = $table;
                continue;
            }

            $status = $this->getStatusLabel($result['status']);
            $difference = $result['difference'];
            
            if ($difference > 0) {
                $difference = '+' . $difference;
            }

            $dataMatch = 'N/A';
            if ($result['data_match'] !== null) {
                $dataMatch = $result['data_match'] ? '✓ Same' : '✗ Different';
            }

            $rows[] = [
                $table,
                number_format($result['mysql_count']),
                number_format($result['pgsql_count']),
                $difference,
                $dataMatch,
                $status
            ];

            if ($result['status'] === 'missing_table') {
                $missingTables[] = $table;
            } elseif ($result['status'] === 'missing_in_pgsql') {
                $missingData[] = $table;
            } elseif ($result['status'] === 'data_different') {
                $dataDifferent[] = $table;
            } elseif ($result['status'] === 'match') {
                $matches[] = $table;
            }
        }

        $this->table($headers, $rows);
        $this->newLine();

        // Summary
        $this->info('=== SUMMARY ===');
        $this->info('Total tables compared: ' . count($results));
        $this->info('✓ Matching tables (same count & data): ' . count($matches));
        if (count($dataDifferent) > 0) {
            $this->warn('⚠ Tables with different data (same count, different content): ' . count($dataDifferent));
        }
        $this->info('⚠ Missing tables in PostgreSQL: ' . count($missingTables));
        $this->info('⚠ Tables with missing data: ' . count($missingData));
        if (count($errors) > 0) {
            $this->error('✗ Tables with errors: ' . count($errors));
        }

        if (count($missingTables) > 0) {
            $this->newLine();
            $this->warn('Missing tables in PostgreSQL:');
            foreach ($missingTables as $table) {
                $this->line('  - ' . $table);
            }
        }

        if (count($missingData) > 0) {
            $this->newLine();
            $this->warn('Tables with missing data in PostgreSQL:');
            foreach ($missingData as $table) {
                $diff = $results[$table]['difference'];
                $this->line("  - {$table} (missing {$diff} records)");
            }
        }

        if (count($dataDifferent) > 0) {
            $this->newLine();
            $this->warn('Tables with different data content (same row count but different values):');
            foreach ($dataDifferent as $table) {
                $this->line("  - {$table}");
            }
        }
    }

    /**
     * Get status label
     *
     * @param string $status
     * @return string
     */
    private function getStatusLabel($status)
    {
        $labels = [
            'match' => '✓ Match',
            'data_different' => '⚠ Data Different',
            'missing_table' => '✗ Table Missing',
            'missing_in_pgsql' => '⚠ Missing Data',
            'extra_in_pgsql' => '⚠ Extra Data',
            'error' => '✗ Error',
            'unknown' => '? Unknown',
        ];

        return $labels[$status] ?? $status;
    }

    /**
     * Generate detailed report with missing record IDs
     *
     * @param array $results
     * @param \Illuminate\Database\Connection $mysqlConnection
     * @param \Illuminate\Database\Connection $pgsqlConnection
     * @return void
     */
    private function generateDetailedReport($results, $mysqlConnection, $pgsqlConnection)
    {
        $this->newLine();
        $this->info('=== DETAILED REPORT ===');
        $this->newLine();

        foreach ($results as $table => $result) {
            if ($result['status'] !== 'missing_in_pgsql' || isset($result['error'])) {
                continue;
            }

            $this->info("Analyzing table: {$table}");
            
            try {
                // Get primary key column
                $primaryKey = $this->getPrimaryKey($table, 'mysql');
                
                if (!$primaryKey) {
                    $this->warn("  Could not determine primary key for table {$table}");
                    continue;
                }

                // Get all IDs from MySQL
                $mysqlIds = $mysqlConnection->table($table)
                    ->pluck($primaryKey)
                    ->toArray();

                // Get all IDs from PostgreSQL
                $pgsqlIds = [];
                if ($this->tableExists($table, 'pgsql')) {
                    $pgsqlIds = $pgsqlConnection->table($table)
                        ->pluck($primaryKey)
                        ->toArray();
                }

                // Find missing IDs
                $missingIds = array_diff($mysqlIds, $pgsqlIds);
                $missingCount = count($missingIds);

                if ($missingCount > 0) {
                    $this->warn("  Missing {$missingCount} records in PostgreSQL");
                    
                    // Show first 10 missing IDs
                    $sampleIds = array_slice($missingIds, 0, 10);
                    $this->line("  Sample missing IDs: " . implode(', ', $sampleIds));
                    
                    if ($missingCount > 10) {
                        $this->line("  ... and " . ($missingCount - 10) . " more");
                    }

                    // Optionally show full details of first few missing records
                    if ($missingCount <= 5) {
                        $this->newLine();
                        $this->line("  Missing records details:");
                        foreach ($sampleIds as $id) {
                            $record = $mysqlConnection->table($table)
                                ->where($primaryKey, $id)
                                ->first();
                            
                            if ($record) {
                                $this->line("    ID {$id}: " . json_encode((array)$record, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                            }
                        }
                    }
                } else {
                    $this->info("  ✓ All records present (count mismatch may be due to duplicates)");
                }

            } catch (\Exception $e) {
                $this->error("  Error analyzing table {$table}: " . $e->getMessage());
            }

            $this->newLine();
        }
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
                // Use parameterized query with proper quoting
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
     * Read tables from markdown file
     *
     * @return array
     */
    private function readTablesFromMarkdown()
    {
        $markdownFile = base_path('postgres_tables_list.md');
        
        if (!File::exists($markdownFile)) {
            $this->warn("Markdown file not found: {$markdownFile}");
            return [];
        }

        $content = File::get($markdownFile);
        $lines = explode("\n", $content);
        $tables = [];

        foreach ($lines as $line) {
            // Match lines like: 1. `table_name` or 1. `table_name` - Status
            if (preg_match('/^\d+\.\s+`([^`]+)`/', $line, $matches)) {
                $tables[] = $matches[1];
            }
        }

        return $tables;
    }
}

