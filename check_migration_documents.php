<?php
/**
 * Script to check if Migration Documents tab has any data
 * 
 * Usage:
 * - Run from command line: php check_migration_documents.php
 * - Or access via browser: http://your-domain/check_migration_documents.php?client_id=JS0jKFMtlyxgCmAK
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Document;
use App\Models\Admin;

// Function to decode client ID (same as in Controller)
function decodeString($string = NULL) {
    if (base64_encode(base64_decode($string, true)) === $string) {
        try {
            $decoded = @convert_uudecode(base64_decode($string));
            if ($decoded === false || $decoded === '') {
                return false;
            }
            return $decoded;
        } catch (\Exception $e) {
            return false;
        }
    }
    return false;
}

echo "=== Migration Documents Data Check ===\n\n";

// Check if client_id is provided via command line argument or query parameter
$clientEncodedId = null;
if (php_sapi_name() === 'cli') {
    // Command line usage
    if (isset($argv[1])) {
        $clientEncodedId = $argv[1];
    }
} else {
    // Web browser usage
    $clientEncodedId = isset($_GET['client_id']) ? $_GET['client_id'] : null;
}

if ($clientEncodedId) {
    // Decode the client ID
    $clientId = decodeString($clientEncodedId);
    
    if ($clientId === false) {
        echo "❌ Error: Invalid encoded client ID: {$clientEncodedId}\n";
        exit(1);
    }
    
    echo "Encoded Client ID: {$clientEncodedId}\n";
    echo "Decoded Client ID: {$clientId}\n\n";
    
    // Check if client exists
    $client = Admin::where('id', $clientId)->where('role', '7')->first();
    if (!$client) {
        echo "❌ Error: Client not found with ID: {$clientId}\n";
        exit(1);
    }
    
    echo "Client Name: {$client->first_name} {$client->last_name}\n";
    echo "Client Email: {$client->email}\n\n";
    
    // Check for migration documents
    $migrationDocs = Document::where('client_id', $clientId)
        ->where('doc_type', 'migration')
        ->where('type', 'client')
        ->orderBy('created_at', 'DESC')
        ->get();
    
    echo "=== Migration Documents Results ===\n";
    echo "Total Migration Documents: " . $migrationDocs->count() . "\n\n";
    
    if ($migrationDocs->count() > 0) {
        echo "✅ Migration Documents tab HAS DATA\n\n";
        echo "Documents List:\n";
        echo str_repeat("-", 80) . "\n";
        printf("%-5s %-30s %-20s %-15s %-10s\n", "ID", "File Name", "Added By", "Added Date", "File Type");
        echo str_repeat("-", 80) . "\n";
        
        foreach ($migrationDocs as $doc) {
            $admin = Admin::select('id', 'first_name')->where('id', $doc->user_id)->first();
            $addedBy = $admin ? $admin->first_name : 'N/A';
            $addedDate = $doc->created_at ? date('d/m/Y', strtotime($doc->created_at)) : 'N/A';
            
            printf("%-5s %-30s %-20s %-15s %-10s\n", 
                $doc->id, 
                substr($doc->file_name . '.' . $doc->filetype, 0, 30),
                substr($addedBy, 0, 20),
                $addedDate,
                $doc->filetype
            );
        }
        echo str_repeat("-", 80) . "\n";
    } else {
        echo "❌ Migration Documents tab HAS NO DATA\n";
        echo "The table will be empty when viewing this tab.\n";
    }
    
} else {
    // No specific client ID provided - show overall statistics
    echo "No specific client ID provided. Showing overall statistics:\n\n";
    
    // Get total count of migration documents
    $totalMigrationDocs = Document::where('doc_type', 'migration')
        ->where('type', 'client')
        ->count();
    
    // Get count of clients with migration documents
    $clientsWithMigrationDocs = Document::where('doc_type', 'migration')
        ->where('type', 'client')
        ->distinct('client_id')
        ->count('client_id');
    
    // Get total clients
    $totalClients = Admin::where('role', '7')->count();
    
    echo "=== Overall Statistics ===\n";
    echo "Total Migration Documents: {$totalMigrationDocs}\n";
    echo "Clients with Migration Documents: {$clientsWithMigrationDocs}\n";
    echo "Total Clients: {$totalClients}\n";
    echo "Clients without Migration Documents: " . ($totalClients - $clientsWithMigrationDocs) . "\n\n";
    
    if ($totalMigrationDocs > 0) {
        echo "✅ There ARE migration documents in the database\n\n";
        echo "Top 10 Clients with Migration Documents:\n";
        echo str_repeat("-", 80) . "\n";
        
        $topClients = DB::table('documents')
            ->select('client_id', DB::raw('COUNT(*) as doc_count'))
            ->where('doc_type', 'migration')
            ->where('type', 'client')
            ->groupBy('client_id')
            ->orderBy('doc_count', 'DESC')
            ->limit(10)
            ->get();
        
        printf("%-10s %-40s %-15s\n", "Client ID", "Client Name", "Doc Count");
        echo str_repeat("-", 80) . "\n";
        
        foreach ($topClients as $item) {
            $client = Admin::find($item->client_id);
            $clientName = $client ? ($client->first_name . ' ' . $client->last_name) : 'N/A';
            printf("%-10s %-40s %-15s\n", 
                $item->client_id,
                substr($clientName, 0, 40),
                $item->doc_count
            );
        }
        echo str_repeat("-", 80) . "\n";
    } else {
        echo "❌ There are NO migration documents in the database\n";
        echo "The Migration Documents tab will be empty for all clients.\n";
    }
    
    echo "\n";
    echo "Usage:\n";
    echo "  php check_migration_documents.php [encoded_client_id]\n";
    echo "  Example: php check_migration_documents.php JS0jKFMtlyxgCmAK\n";
    echo "\n";
    echo "Or via browser:\n";
    echo "  http://your-domain/check_migration_documents.php?client_id=JS0jKFMtlyxgCmAK\n";
}

echo "\n=== Check Complete ===\n";

