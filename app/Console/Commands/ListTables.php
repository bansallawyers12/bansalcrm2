<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ListTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:list-tables {--connection=mysql : Database connection to use}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all tables in the database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $connection = $this->option('connection');
        
        try {
            $tables = $this->getAllTables($connection);
            
            if (empty($tables)) {
                $this->warn("No tables found in the {$connection} database.");
                return 0;
            }

            $this->info("Found " . count($tables) . " tables in the {$connection} database:");
            $this->newLine();
            
            foreach ($tables as $index => $table) {
                $this->line(($index + 1) . ". " . $table);
            }
            
            return 0;
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
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
        
        sort($tables);
        return $tables;
    }
}
