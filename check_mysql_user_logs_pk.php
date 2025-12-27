<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Checking user_logs table structure in MySQL...\n\n";

try {
    // Connect to MySQL
    $mysqlConnection = DB::connection('mysql');
    echo "✅ Connected to MySQL\n\n";
    
    // Check table structure
    $columns = $mysqlConnection->select("SHOW COLUMNS FROM user_logs");
    
    echo "Columns in user_logs table (MySQL):\n";
    echo str_repeat("-", 100) . "\n";
    printf("%-20s %-20s %-10s %-10s %-10s %-20s\n", "Field", "Type", "Null", "Key", "Default", "Extra");
    echo str_repeat("-", 100) . "\n";
    
    foreach($columns as $col) {
        printf("%-20s %-20s %-10s %-10s %-10s %-20s\n", 
            $col->Field, 
            $col->Type, 
            $col->Null,
            $col->Key ?? 'N/A',
            $col->Default ?? 'NULL',
            $col->Extra ?? ''
        );
    }
    
    echo "\n" . str_repeat("-", 100) . "\n\n";
    
    // Check for primary key
    $indexes = $mysqlConnection->select("SHOW INDEXES FROM user_logs WHERE Key_name = 'PRIMARY'");
    
    if (count($indexes) > 0) {
        echo "✅ Primary Key Found in MySQL:\n";
        foreach($indexes as $index) {
            echo "  - Key Name: {$index->Key_name}\n";
            echo "  - Column: {$index->Column_name}\n";
            echo "  - Non Unique: {$index->Non_unique}\n";
            echo "  - Seq In Index: {$index->Seq_in_index}\n";
        }
    } else {
        echo "❌ NO Primary Key Found in MySQL!\n";
    }
    
    echo "\n" . str_repeat("-", 100) . "\n\n";
    
    // Check all indexes
    $allIndexes = $mysqlConnection->select("SHOW INDEXES FROM user_logs");
    
    if (count($allIndexes) > 0) {
        echo "All Indexes on user_logs table (MySQL):\n";
        $grouped = [];
        foreach($allIndexes as $index) {
            $keyName = $index->Key_name;
            if (!isset($grouped[$keyName])) {
                $grouped[$keyName] = [];
            }
            $grouped[$keyName][] = $index;
        }
        
        foreach($grouped as $keyName => $indexes) {
            echo "  - Index Name: {$keyName}\n";
            foreach($indexes as $idx) {
                echo "    * Column: {$idx->Column_name}, Non-unique: {$idx->Non_unique}, Sequence: {$idx->Seq_in_index}\n";
            }
        }
    } else {
        echo "  No indexes found.\n";
    }
    
    echo "\n" . str_repeat("-", 100) . "\n\n";
    
    // Check CREATE TABLE statement to see AUTO_INCREMENT
    $createTable = $mysqlConnection->select("SHOW CREATE TABLE user_logs");
    if (count($createTable) > 0) {
        echo "CREATE TABLE Statement (MySQL):\n";
        echo str_repeat("-", 100) . "\n";
        echo $createTable[0]->{'Create Table'} . "\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Error connecting to MySQL: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
    
    echo "\n" . str_repeat("=", 100) . "\n";
    echo "Trying to check MySQL connection configuration...\n";
    
    try {
        $config = config('database.connections.mysql');
        echo "MySQL Config:\n";
        echo "  Host: " . ($config['host'] ?? 'N/A') . "\n";
        echo "  Port: " . ($config['port'] ?? 'N/A') . "\n";
        echo "  Database: " . ($config['database'] ?? 'N/A') . "\n";
        echo "  Username: " . ($config['username'] ?? 'N/A') . "\n";
    } catch (\Exception $e2) {
        echo "Could not read config: " . $e2->getMessage() . "\n";
    }
}

