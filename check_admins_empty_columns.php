<?php
/**
 * Report all columns in admins table that are empty (0 or negligible non-empty rows).
 * Run: php check_admins_empty_columns.php
 */
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$total = DB::table('admins')->count();
echo "Total admins: $total\n\n";

$columns = Schema::getColumnListing('admins');
sort($columns);

$emptyColumns = [];
$nonEmptyCounts = [];

$numericOrDateTypes = ['integer', 'bigint', 'smallint', 'boolean', 'date', 'datetime', 'timestamp', 'float', 'double', 'decimal', 'real'];

foreach ($columns as $col) {
    $type = Schema::getColumnType('admins', $col);
    $isNumericOrDate = $type && in_array($type, $numericOrDateTypes, true);

    try {
        if ($isNumericOrDate) {
            // Non-empty = NOT NULL (0 is valid for status, FKs, etc.)
            $nonEmpty = DB::table('admins')->whereNotNull($col)->count();
        } else {
            // string, text, etc.: non-empty = NOT NULL and != ''
            $nonEmpty = DB::table('admins')->whereNotNull($col)->where($col, '!=', '')->count();
        }
    } catch (\Throwable $e) {
        // e.g. PostgreSQL int column compared to '' - fallback to whereNotNull only
        $nonEmpty = DB::table('admins')->whereNotNull($col)->count();
    }

    $nonEmptyCounts[$col] = $nonEmpty;
    if ($nonEmpty === 0) {
        $emptyColumns[] = $col;
    }
}

echo "--- Columns with ZERO non-empty values (empty) ---\n";
foreach ($emptyColumns as $c) {
    echo "  $c\n";
}
echo "\nTotal empty columns: " . count($emptyColumns) . " / " . count($columns) . "\n";

echo "\n--- All columns: non-empty count (empty listed first) ---\n";
// Sort so 0s first, then by count ascending
uasort($nonEmptyCounts, function ($a, $b) {
    if ($a === 0 && $b !== 0) return -1;
    if ($a !== 0 && $b === 0) return 1;
    return $a <=> $b;
});
foreach ($nonEmptyCounts as $col => $count) {
    $pct = $total ? round(100 * $count / $total, 2) : 0;
    $tag = $count === 0 ? ' [EMPTY]' : '';
    echo sprintf("  %-30s %6d  (%5s%%)%s\n", $col, $count, $pct, $tag);
}
