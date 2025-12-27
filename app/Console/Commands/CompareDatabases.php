<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CompareDatabases extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:compare {--table= : Compare specific table only} {--detailed : Show detailed missing records}';

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
            $tables = $tableName ? [$tableName] : $this->getAllTables('mysql');

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
                return $result;
            }

            // Get PostgreSQL count
            $pgsqlCount = $pgsqlConnection->table($table)->count();
            $result['pgsql_count'] = $pgsqlCount;
            $result['difference'] = $mysqlCount - $pgsqlCount;

            if ($mysqlCount === $pgsqlCount) {
                $result['status'] = 'match';
            } elseif ($mysqlCount > $pgsqlCount) {
                $result['status'] = 'missing_in_pgsql';
            } else {
                $result['status'] = 'extra_in_pgsql';
            }

        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            $result['status'] = 'error';
        }

        return $result;
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

        $headers = ['Table', 'MySQL Count', 'PostgreSQL Count', 'Difference', 'Status'];
        $rows = [];

        $missingTables = [];
        $missingData = [];
        $matches = [];
        $errors = [];

        foreach ($results as $table => $result) {
            if (isset($result['error'])) {
                $rows[] = [
                    $table,
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

            $rows[] = [
                $table,
                number_format($result['mysql_count']),
                number_format($result['pgsql_count']),
                $difference,
                $status
            ];

            if ($result['status'] === 'missing_table') {
                $missingTables[] = $table;
            } elseif ($result['status'] === 'missing_in_pgsql') {
                $missingData[] = $table;
            } elseif ($result['status'] === 'match') {
                $matches[] = $table;
            }
        }

        $this->table($headers, $rows);
        $this->newLine();

        // Summary
        $this->info('=== SUMMARY ===');
        $this->info('Total tables compared: ' . count($results));
        $this->info('✓ Matching tables: ' . count($matches));
        $this->info('⚠ Missing tables in PostgreSQL: ' . count($missingTables));
        $this->info('⚠ Tables with missing data: ' . count($missingData));
        if (count($errors) > 0) {
            $this->info('✗ Tables with errors: ' . count($errors));
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
}

