<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$total = DB::table('admins')->count();
echo "Total admins: $total\n\n";

// Summary-table columns (position, telephone, time_zone, user_id, profile_img)
echo "--- Non-empty counts (admins table) ---\n";
foreach (['position', 'telephone', 'time_zone', 'profile_img'] as $col) {
	$count = DB::table('admins')->whereNotNull($col)->where($col, '!=', '')->count();
	$pct = $total ? round(100 * $count / $total, 1) : 0;
	echo sprintf("%-12s : %d / %d (%s%%)\n", $col, $count, $total, $pct);
}
// user_id is integer: count non-null and > 0
$userIdCount = DB::table('admins')->whereNotNull('user_id')->where('user_id', '>', 0)->count();
echo sprintf("%-12s : %d / %d (%s%%)\n", 'user_id', $userIdCount, $total, $total ? round(100 * $userIdCount / $total, 1) : 0);

$withCompanyName = DB::table('admins')->whereNotNull('company_name')->where('company_name', '!=', '')->count();
$withCompanyWebsite = DB::table('admins')->whereNotNull('company_website')->where('company_website', '!=', '')->count();
$withStaffId = DB::table('admins')->whereNotNull('staff_id')->where('staff_id', '!=', '')->count();
$sample = DB::table('admins')->whereNotNull('company_name')->where('company_name', '!=', '')->select('id','company_name','company_website')->limit(5)->get();

echo "\nRows with company_name set: $withCompanyName\n";
echo "Rows with company_website set: $withCompanyWebsite\n";
echo "Rows with staff_id set: $withStaffId\n";
echo "Sample (first 5):\n";
foreach ($sample as $r) { echo "  id={$r->id} company_name=" . substr($r->company_name ?? '', 0, 50) . " company_website=" . ($r->company_website ?? '') . "\n"; }
