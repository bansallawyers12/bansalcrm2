<?php
/**
 * Check data in staff_id, time_zone, position, telephone columns of admins table.
 * Run: php check_admin_columns_data.php
 */
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$total = DB::table('admins')->count();
echo "=== Admins table - staff_id, time_zone, position, telephone ===\n\n";
echo "Total admins: $total\n\n";

$columns = ['staff_id', 'time_zone', 'position', 'telephone'];

foreach ($columns as $col) {
    $filled = DB::table('admins')->whereNotNull($col)->where($col, '!=', '')->count();
    $nullCount = DB::table('admins')->whereNull($col)->count();
    $emptyStr = DB::table('admins')->where($col, '=', '')->count();
    $pct = $total ? round(100 * $filled / $total, 1) : 0;
    echo "--- $col ---\n";
    echo sprintf("  Non-empty: %d (%s%%)\n", $filled, $pct);
    echo sprintf("  NULL: %d\n", $nullCount);
    echo sprintf("  Empty string: %d\n", $emptyStr);
    if ($filled > 0 && $filled <= 10) {
        $samples = DB::table('admins')->whereNotNull($col)->where($col, '!=', '')->select('id', $col)->limit(5)->get();
        echo "  Sample: ";
        foreach ($samples as $r) {
            echo "id={$r->id} " . substr((string)$r->{$col}, 0, 40) . "; ";
        }
        echo "\n";
    }
    echo "\n";
}
