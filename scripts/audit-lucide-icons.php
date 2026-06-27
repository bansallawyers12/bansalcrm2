<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$iconsDir = base_path('node_modules/lucide/dist/esm/icons');
$allNames = [];

$paths = [
    base_path('resources/views'),
    base_path('public/js'),
    base_path('app'),
];

foreach ($paths as $root) {
    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));
    foreach ($rii as $file) {
        if (! $file->isFile()) {
            continue;
        }
        $ext = $file->getExtension();
        if (! in_array($ext, ['php', 'blade.php', 'js'], true)) {
            continue;
        }
        $content = file_get_contents($file->getPathname());
        if (preg_match_all("/@icon\\(['\"]([^'\"]+)['\"]/", $content, $m)) {
            foreach ($m[1] as $n) {
                $allNames[$n] = true;
            }
        }
        if (preg_match_all("/IconHelper::render\\(['\"]([^'\"]+)['\"]/", $content, $m2)) {
            foreach ($m2[1] as $n) {
                $allNames[$n] = true;
            }
        }
        if (preg_match_all("/crmIcon\\(['\"]([^'\"]+)['\"]/", $content, $m3)) {
            foreach ($m3[1] as $n) {
                $allNames[$n] = true;
            }
        }
    }
}

ksort($allNames);
$bad = [];
foreach (array_keys($allNames) as $name) {
    $lucide = \App\Helpers\IconHelper::lucideName($name);
    if (! file_exists($iconsDir . '/' . $lucide . '.mjs')) {
        $bad[$name] = $lucide;
    }
}

echo 'Total unique icon names: ' . count($allNames) . PHP_EOL;
echo 'Broken Lucide targets: ' . count($bad) . PHP_EOL;
foreach ($bad as $name => $lucide) {
    echo "  $name => $lucide\n";
}
