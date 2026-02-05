<?php
/**
 * One-off script to check row counts and sample data in checklist/application tables.
 * Run: php check_table_data.php
 * Delete this file after use.
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$tables = [
    'checklists',
    'document_checklists',
    'upload_checklists',
    'application_document_lists',
    'application_documents',
    'application_activities_logs',
];

echo "=== Table row counts ===\n\n";

foreach ($tables as $table) {
    try {
        $count = \DB::table($table)->count();
        echo sprintf("%-35s %d rows\n", $table . ':', $count);
    } catch (\Throwable $e) {
        echo sprintf("%-35s ERROR: %s\n", $table . ':', $e->getMessage());
    }
}

echo "\n=== Sample data (first 3 rows per table) ===\n\n";

foreach ($tables as $table) {
    try {
        $count = \DB::table($table)->count();
        if ($count === 0) {
            echo "--- $table: (empty)\n\n";
            continue;
        }
        $cols = \Schema::getColumnListing($table);
        $rows = \DB::table($table)->limit(3)->get();
        echo "--- $table (columns: " . implode(', ', $cols) . ")\n";
        foreach ($rows as $i => $row) {
            echo "  Row " . ($i + 1) . ": " . json_encode($row, JSON_UNESCAPED_SLASHES) . "\n";
        }
        if ($count > 3) {
            echo "  ... and " . ($count - 3) . " more row(s)\n";
        }
        echo "\n";
    } catch (\Throwable $e) {
        echo "--- $table: ERROR " . $e->getMessage() . "\n\n";
    }
}

echo "=== Related: documents table (checklist column) ===\n\n";
try {
    $docWithChecklist = \DB::table('documents')->whereNotNull('checklist')->where('checklist', '!=', '')->count();
    $docTotal = \DB::table('documents')->count();
    echo sprintf("documents with non-empty checklist: %d\n", $docWithChecklist);
    echo sprintf("documents total: %d\n", $docTotal);
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== application_activities_logs by type ===\n\n";
try {
    $types = \DB::table('application_activities_logs')->select('type', \DB::raw('count(*) as c'))->groupBy('type')->get();
    foreach ($types as $t) {
        echo sprintf("  %-20s %d rows\n", $t->type . ':', $t->c);
    }
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "\nDone.\n";
