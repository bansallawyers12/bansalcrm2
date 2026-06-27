<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$iconsDir = base_path('node_modules/lucide/dist/esm/icons');
$map = config('icons.fa_to_lucide', []);

$missingLucide = [];
foreach ($map as $fa => $lucide) {
    if (! file_exists($iconsDir . '/' . $lucide . '.mjs')) {
        $missingLucide[$fa] = $lucide;
    }
}

echo "Missing Lucide files (" . count($missingLucide) . "):\n";
foreach ($missingLucide as $fa => $lucide) {
    echo "  $fa => $lucide\n";
}

// Extract @icon('name') from blades
$bladeIcons = [];
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(base_path('resources/views')));
foreach ($rii as $file) {
    if (! $file->isFile() || ! str_ends_with($file->getFilename(), '.blade.php')) {
        continue;
    }
    $content = file_get_contents($file->getPathname());
    if (preg_match_all("/@icon\\(['\"]([^'\"]+)['\"]/", $content, $m)) {
        foreach ($m[1] as $name) {
            $bladeIcons[$name] = true;
        }
    }
    if (preg_match_all("/IconHelper::render\\(['\"]([^'\"]+)['\"]/", $content, $m2)) {
        foreach ($m2[1] as $name) {
            $bladeIcons[$name] = true;
        }
    }
}

$unmapped = [];
$badLucide = [];
foreach (array_keys($bladeIcons) as $name) {
    $lucide = \App\Helpers\IconHelper::lucideName($name);
    if (! isset($map[$name]) && ! isset($map[\App\Helpers\IconHelper::nameFromClassString('fas fa-' . $name) ?? ''])) {
        // identity fallback ok if lucide file exists
    }
    if (! file_exists($iconsDir . '/' . $lucide . '.mjs')) {
        $badLucide[$name] = $lucide;
    }
}

echo "\nBlade icons with missing Lucide target (" . count($badLucide) . "):\n";
foreach ($badLucide as $name => $lucide) {
    echo "  @icon('$name') => $lucide\n";
}

// crmIcon names from JS
$jsIcons = [];
$jsDir = base_path('public/js');
$jsRii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($jsDir));
foreach ($jsRii as $file) {
    if (! $file->isFile() || ! str_ends_with($file->getFilename(), '.js')) {
        continue;
    }
    $content = file_get_contents($file->getPathname());
    if (preg_match_all("/crmIcon\\(['\"]([^'\"]+)['\"]/", $content, $m)) {
        foreach ($m[1] as $name) {
            $jsIcons[$name] = true;
        }
    }
}

$badJs = [];
foreach (array_keys($jsIcons) as $name) {
    $lucide = $map[$name] ?? str_replace('_', '-', $name);
    if (! file_exists($iconsDir . '/' . $lucide . '.mjs')) {
        $badJs[$name] = $lucide;
    }
}

echo "\nJS crmIcon with missing Lucide target (" . count($badJs) . "):\n";
foreach ($badJs as $name => $lucide) {
    echo "  crmIcon('$name') => $lucide\n";
}
