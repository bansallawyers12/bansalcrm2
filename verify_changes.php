<?php
/**
 * Verification Script for URL Restructure Changes
 * 
 * This script verifies that all changes have been applied correctly.
 * 
 * Usage: php verify_changes.php
 */

$baseDir = __DIR__;

echo "========================================\n";
echo "URL Restructure - Verification Script\n";
echo "========================================\n\n";

$issues = [];
$warnings = [];

// ============================================================================
// 1. VERIFY ROUTES
// ============================================================================
echo "[1] Verifying Routes (routes/web.php)...\n";
echo "-----------------------------------\n";

$webRoutes = file_get_contents($baseDir . '/routes/web.php');

// Check: No admin prefix group
if (preg_match("/Route::prefix\(['\"]admin['\"]\)->group/", $webRoutes)) {
    $issues[] = "❌ Found Route::prefix('admin')->group() - should be removed";
} else {
    echo "✓ No admin prefix group found\n";
}

// Check: Admin login routes exist
if (preg_match("/Route::get\(['\"]\/admin['\"].*->name\(['\"]admin\.login['\"]\)/", $webRoutes)) {
    echo "✓ Admin login route exists\n";
} else {
    $issues[] = "❌ Admin login route missing";
}

// Check: Route names (should have minimal admin.* routes)
$adminRouteNames = preg_match_all("/->name\(['\"]admin\.(?!login|logout)/", $webRoutes);
if ($adminRouteNames > 0) {
    $warnings[] = "⚠ Found $adminRouteNames route names still using admin.* prefix (excluding login/logout)";
} else {
    echo "✓ Route names updated correctly\n";
}

// ============================================================================
// 2. VERIFY CSRF EXCEPTIONS
// ============================================================================
echo "\n[2] Verifying CSRF Exceptions (bootstrap/app.php)...\n";
echo "-----------------------------------\n";

$appConfig = file_get_contents($baseDir . '/bootstrap/app.php');

if (preg_match("/'admin\//", $appConfig)) {
    $issues[] = "❌ Found 'admin/' in CSRF exceptions - should be removed";
} else {
    echo "✓ CSRF exceptions updated correctly\n";
}

// ============================================================================
// 3. VERIFY VIEW FILES
// ============================================================================
echo "\n[3] Verifying View Files...\n";
echo "-----------------------------------\n";

function countPatternInFiles($directory, $pattern, $excludePatterns = []) {
    $count = 0;
    $files = [];
    
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    
    foreach ($iterator as $file) {
        if ($file->isFile() && preg_match('/\.(blade\.php|php)$/', $file->getExtension())) {
            $filePath = $file->getPathname();
            
            // Check exclusions
            $shouldExclude = false;
            foreach ($excludePatterns as $exclude) {
                if (strpos($filePath, $exclude) !== false) {
                    $shouldExclude = true;
                    break;
                }
            }
            
            if ($shouldExclude) {
                continue;
            }
            
            $content = file_get_contents($filePath);
            $matches = preg_match_all($pattern, $content);
            if ($matches > 0) {
                $count += $matches;
                $files[] = ['file' => $filePath, 'count' => $matches];
            }
        }
    }
    
    return ['total' => $count, 'files' => $files];
}

// Check for remaining admin.* routes (excluding login/logout)
$routeCheck = countPatternInFiles(
    $baseDir . '/resources/views',
    "/route\(['\"](admin\.(?!login|logout|clients\.|adminconsole\.))/",
    ['/AdminConsole/', '/auth/admin-login.blade.php']
);

if ($routeCheck['total'] > 0) {
    $warnings[] = "⚠ Found {$routeCheck['total']} remaining route('admin.*) references (excluding login/logout/clients/adminconsole)";
    echo "  Files with issues:\n";
    foreach (array_slice($routeCheck['files'], 0, 10) as $file) {
        echo "    - {$file['file']} ({$file['count']} matches)\n";
    }
    if (count($routeCheck['files']) > 10) {
        echo "    ... and " . (count($routeCheck['files']) - 10) . " more files\n";
    }
} else {
    echo "✓ No remaining route('admin.*) references found\n";
}

// Check for remaining /admin/ URLs
$urlCheck = countPatternInFiles(
    $baseDir . '/resources/views',
    "/(url|URL::to)\(['\"]\/admin\/(?!adminconsole)/",
    ['/AdminConsole/']
);

if ($urlCheck['total'] > 0) {
    $warnings[] = "⚠ Found {$urlCheck['total']} remaining url('/admin/') references";
    echo "  Files with issues:\n";
    foreach (array_slice($urlCheck['files'], 0, 10) as $file) {
        echo "    - {$file['file']} ({$file['count']} matches)\n";
    }
    if (count($urlCheck['files']) > 10) {
        echo "    ... and " . (count($urlCheck['files']) - 10) . " more files\n";
    }
} else {
    echo "✓ No remaining url('/admin/') references found\n";
}

// ============================================================================
// 4. VERIFY JAVASCRIPT FILES
// ============================================================================
echo "\n[4] Verifying JavaScript Files...\n";
echo "-----------------------------------\n";

$jsCheck = countPatternInFiles(
    $baseDir . '/public/js',
    "/['\"](\/admin\/|'\/admin\/|\"\/admin\/)/",
    []
);

if ($jsCheck['total'] > 0) {
    $warnings[] = "⚠ Found {$jsCheck['total']} remaining '/admin/' references in JS files";
    echo "  Files with issues:\n";
    foreach (array_slice($jsCheck['files'], 0, 10) as $file) {
        echo "    - {$file['file']} ({$file['count']} matches)\n";
    }
    if (count($jsCheck['files']) > 10) {
        echo "    ... and " . (count($jsCheck['files']) - 10) . " more files\n";
    }
} else {
    echo "✓ No remaining '/admin/' references in JS files\n";
}

// ============================================================================
// 5. VERIFY CONTROLLERS
// ============================================================================
echo "\n[5] Verifying Controller Files...\n";
echo "-----------------------------------\n";

$controllerCheck = countPatternInFiles(
    $baseDir . '/app/Http/Controllers/Admin',
    "/(redirect\(\)->route|route)\(['\"]admin\.(?!login|logout|clients\.)/",
    []
);

if ($controllerCheck['total'] > 0) {
    $warnings[] = "⚠ Found {$controllerCheck['total']} remaining admin.* route references in controllers";
    echo "  Files with issues:\n";
    foreach (array_slice($controllerCheck['files'], 0, 10) as $file) {
        echo "    - {$file['file']} ({$file['count']} matches)\n";
    }
    if (count($controllerCheck['files']) > 10) {
        echo "    ... and " . (count($controllerCheck['files']) - 10) . " more files\n";
    }
} else {
    echo "✓ No remaining admin.* route references in controllers\n";
}

// ============================================================================
// SUMMARY
// ============================================================================
echo "\n========================================\n";
echo "VERIFICATION SUMMARY\n";
echo "========================================\n";

if (empty($issues) && empty($warnings)) {
    echo "✅ All verifications passed!\n";
} else {
    if (!empty($issues)) {
        echo "\n❌ CRITICAL ISSUES:\n";
        foreach ($issues as $issue) {
            echo "  $issue\n";
        }
    }
    
    if (!empty($warnings)) {
        echo "\n⚠ WARNINGS:\n";
        foreach ($warnings as $warning) {
            echo "  $warning\n";
        }
    }
}

echo "\n========================================\n";
echo "\n";

