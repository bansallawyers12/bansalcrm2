<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$sidebarFile = base_path('resources/views/Elements/Admin/left-side-bar.blade.php');
$content = file_get_contents($sidebarFile);
preg_match_all("/@icon\\(['\"]([^'\"]+)['\"]/", $content, $m);
$icons = array_unique($m[1]);
sort($icons);

$iconsDir = base_path('node_modules/lucide/dist/esm/icons');
echo "Sidebar @icon() names in left-side-bar.blade.php:\n";
foreach ($icons as $name) {
    $lucide = \App\Helpers\IconHelper::lucideName($name);
    $exists = file_exists($iconsDir . '/' . $lucide . '.mjs');
    echo ($exists ? 'OK  ' : 'MISS') . "  {$name} => {$lucide}\n";
}

echo "\nRendered sample:\n";
echo \App\Helpers\IconHelper::render('desktop') . "\n";
