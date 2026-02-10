<?php
/**
 * Check data in the office_id column of the admins table.
 * Run: php check_admin_office_id.php
 */
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$total = DB::table('admins')->count();
echo "=== Admins table - office_id column ===\n\n";
echo "Total admins: $total\n\n";

// Null vs non-null
$nullCount = DB::table('admins')->whereNull('office_id')->count();
$nonNullCount = DB::table('admins')->whereNotNull('office_id')->count();

echo "--- Summary ---\n";
echo sprintf("  office_id NULL    : %d (%s%%)\n", $nullCount, $total ? round(100 * $nullCount / $total, 1) : 0);
echo sprintf("  office_id NOT NULL: %d (%s%%)\n", $nonNullCount, $total ? round(100 * $nonNullCount / $total, 1) : 0);

// Unique office_id values with counts
$distribution = DB::table('admins')
    ->whereNotNull('office_id')
    ->select('office_id', DB::raw('count(*) as cnt'))
    ->groupBy('office_id')
    ->orderBy('cnt', 'desc')
    ->get();

echo "\n--- office_id values (non-null) ---\n";
if ($distribution->isEmpty()) {
    echo "  (no non-null values)\n";
} else {
    foreach ($distribution as $row) {
        echo sprintf("  office_id=%s : %d rows\n", $row->office_id, $row->cnt);
    }
}

// Cross-check with branches table
$branchIds = DB::table('branches')->pluck('id')->toArray();
$invalidIds = [];
foreach ($distribution as $row) {
    if (!in_array((int) $row->office_id, $branchIds, true)) {
        $invalidIds[] = $row->office_id;
    }
}

if (!empty($invalidIds)) {
    echo "\n--- WARNING: office_ids NOT in branches table ---\n";
    foreach ($invalidIds as $id) {
        echo "  office_id=$id (orphaned)\n";
    }
} else {
    echo "\n--- All office_ids exist in branches table ---\n";
}

// Sample rows (id, first_name, last_name, role, office_id)
echo "\n--- Sample rows (first 10 with office_id) ---\n";
$sample = DB::table('admins')
    ->whereNotNull('office_id')
    ->select('id', 'first_name', 'last_name', 'role', 'office_id')
    ->limit(10)
    ->get();

foreach ($sample as $r) {
    echo sprintf("  id=%d  %s %s  role=%s  office_id=%s\n",
        $r->id,
        $r->first_name ?? '',
        $r->last_name ?? '',
        $r->role ?? 'null',
        $r->office_id ?? 'null'
    );
}

// Sample rows with NULL office_id
echo "\n--- Sample rows with NULL office_id (first 5) ---\n";
$sampleNull = DB::table('admins')
    ->whereNull('office_id')
    ->select('id', 'first_name', 'last_name', 'role')
    ->limit(5)
    ->get();

foreach ($sampleNull as $r) {
    echo sprintf("  id=%d  %s %s  role=%s\n",
        $r->id,
        $r->first_name ?? '',
        $r->last_name ?? '',
        $r->role ?? 'null'
    );
}
