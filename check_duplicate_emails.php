<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$results = DB::select("
    SELECT email, COUNT(*) as count
    FROM admins
    WHERE email IS NOT NULL AND email != ''
    GROUP BY email
    HAVING COUNT(*) > 1
    ORDER BY count DESC
");

echo "Duplicate emails in admins table:\n";
echo str_repeat('-', 50) . "\n";
foreach ($results as $row) {
    echo $row->email . " (count: " . $row->count . ")\n";
}
if (empty($results)) {
    echo "No duplicate emails found.\n";
}
