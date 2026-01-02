<?php
/**
 * Route Reference Audit Script
 * Finds all references to /admin/clients and /agent/clients routes
 * 
 * Usage: php audit_route_references.php
 */

$baseDir = __DIR__;
$results = [
    'blade' => [],
    'javascript' => [],
    'php' => [],
    'hardcoded_urls' => []
];

// Patterns to search for
$patterns = [
    '/admin/clients',
    '/agent/clients',
    'admin.clients.',
    'agent.clients.',
    'route\([\'"]admin\.clients',
    'route\([\'"]agent\.clients',
    'url\([\'"]\/admin\/clients',
    'url\([\'"]\/agent\/clients',
    'href=[\'"]\/admin\/clients',
    'href=[\'"]\/agent\/clients',
    'action=[\'"]\/admin\/clients',
    'action=[\'"]\/agent\/clients',
];

// Search Blade files
echo "Searching Blade files...\n";
$bladeFiles = glob_recursive($baseDir . '/resources/views/**/*.blade.php');
foreach ($bladeFiles as $file) {
    $content = file_get_contents($file);
    $relativePath = str_replace($baseDir . '/', '', $file);
    
    foreach ($patterns as $pattern) {
        if (preg_match_all('/' . preg_quote($pattern, '/') . '/i', $content, $matches, PREG_OFFSET_CAPTURE)) {
            if (!isset($results['blade'][$relativePath])) {
                $results['blade'][$relativePath] = [];
            }
            foreach ($matches[0] as $match) {
                $line = substr_count(substr($content, 0, $match[1]), "\n") + 1;
                $results['blade'][$relativePath][] = [
                    'pattern' => $pattern,
                    'line' => $line,
                    'match' => substr($content, max(0, $match[1] - 50), 100)
                ];
            }
        }
    }
}

// Search JavaScript files
echo "Searching JavaScript files...\n";
$jsFiles = array_merge(
    glob_recursive($baseDir . '/public/js/**/*.js'),
    glob_recursive($baseDir . '/resources/js/**/*.js')
);
foreach ($jsFiles as $file) {
    $content = file_get_contents($file);
    $relativePath = str_replace($baseDir . '/', '', $file);
    
    foreach ($patterns as $pattern) {
        if (preg_match_all('/' . preg_quote($pattern, '/') . '/i', $content, $matches, PREG_OFFSET_CAPTURE)) {
            if (!isset($results['javascript'][$relativePath])) {
                $results['javascript'][$relativePath] = [];
            }
            foreach ($matches[0] as $match) {
                $line = substr_count(substr($content, 0, $match[1]), "\n") + 1;
                $results['javascript'][$relativePath][] = [
                    'pattern' => $pattern,
                    'line' => $line,
                    'match' => substr($content, max(0, $match[1] - 50), 100)
                ];
            }
        }
    }
}

// Search PHP files (controllers, routes, etc.)
echo "Searching PHP files...\n";
$phpFiles = array_merge(
    glob_recursive($baseDir . '/app/**/*.php'),
    glob_recursive($baseDir . '/routes/**/*.php')
);
foreach ($phpFiles as $file) {
    $content = file_get_contents($file);
    $relativePath = str_replace($baseDir . '/', '', $file);
    
    foreach ($patterns as $pattern) {
        if (preg_match_all('/' . preg_quote($pattern, '/') . '/i', $content, $matches, PREG_OFFSET_CAPTURE)) {
            if (!isset($results['php'][$relativePath])) {
                $results['php'][$relativePath] = [];
            }
            foreach ($matches[0] as $match) {
                $line = substr_count(substr($content, 0, $match[1]), "\n") + 1;
                $results['php'][$relativePath][] = [
                    'pattern' => $pattern,
                    'line' => $line,
                    'match' => substr($content, max(0, $match[1] - 50), 100)
                ];
            }
        }
    }
}

// Find hardcoded URLs
echo "Finding hardcoded URLs...\n";
$urlPatterns = [
    '/admin/clients',
    '/agent/clients'
];
foreach (array_merge($bladeFiles, $jsFiles, $phpFiles) as $file) {
    $content = file_get_contents($file);
    $relativePath = str_replace($baseDir . '/', '', $file);
    
    foreach ($urlPatterns as $urlPattern) {
        if (strpos($content, $urlPattern) !== false) {
            $lines = explode("\n", $content);
            foreach ($lines as $lineNum => $line) {
                if (strpos($line, $urlPattern) !== false) {
                    if (!isset($results['hardcoded_urls'][$relativePath])) {
                        $results['hardcoded_urls'][$relativePath] = [];
                    }
                    $results['hardcoded_urls'][$relativePath][] = [
                        'url' => $urlPattern,
                        'line' => $lineNum + 1,
                        'content' => trim($line)
                    ];
                }
            }
        }
    }
}

// Generate report
echo "\n=== ROUTE REFERENCE AUDIT REPORT ===\n\n";
echo "Blade Files: " . count($results['blade']) . " files\n";
echo "JavaScript Files: " . count($results['javascript']) . " files\n";
echo "PHP Files: " . count($results['php']) . " files\n";
echo "Files with Hardcoded URLs: " . count($results['hardcoded_urls']) . " files\n\n";

// Save detailed report
$report = "# Route Reference Audit Report\n\n";
$report .= "Generated: " . date('Y-m-d H:i:s') . "\n\n";
$report .= "## Summary\n\n";
$report .= "- Blade Files: " . count($results['blade']) . "\n";
$report .= "- JavaScript Files: " . count($results['javascript']) . "\n";
$report .= "- PHP Files: " . count($results['php']) . "\n";
$report .= "- Files with Hardcoded URLs: " . count($results['hardcoded_urls']) . "\n\n";

$report .= "## Blade Files (" . count($results['blade']) . " files)\n\n";
foreach ($results['blade'] as $file => $matches) {
    $report .= "### $file\n";
    $report .= "Found " . count($matches) . " references\n\n";
    foreach ($matches as $match) {
        $report .= "- Line {$match['line']}: Pattern `{$match['pattern']}`\n";
        $report .= "  Context: `" . trim($match['match']) . "`\n\n";
    }
}

$report .= "\n## JavaScript Files (" . count($results['javascript']) . " files)\n\n";
foreach ($results['javascript'] as $file => $matches) {
    $report .= "### $file\n";
    $report .= "Found " . count($matches) . " references\n\n";
    foreach ($matches as $match) {
        $report .= "- Line {$match['line']}: Pattern `{$match['pattern']}`\n";
        $report .= "  Context: `" . trim($match['match']) . "`\n\n";
    }
}

$report .= "\n## PHP Files (" . count($results['php']) . " files)\n\n";
foreach ($results['php'] as $file => $matches) {
    $report .= "### $file\n";
    $report .= "Found " . count($matches) . " references\n\n";
    foreach ($matches as $match) {
        $report .= "- Line {$match['line']}: Pattern `{$match['pattern']}`\n";
        $report .= "  Context: `" . trim($match['match']) . "`\n\n";
    }
}

$report .= "\n## Hardcoded URLs\n\n";
foreach ($results['hardcoded_urls'] as $file => $urls) {
    $report .= "### $file\n\n";
    foreach ($urls as $url) {
        $report .= "- Line {$url['line']}: `{$url['url']}`\n";
        $report .= "  `{$url['content']}`\n\n";
    }
}

file_put_contents($baseDir . '/ROUTE_REFERENCE_AUDIT.md', $report);
echo "Report saved to ROUTE_REFERENCE_AUDIT.md\n";

// Helper function for recursive glob
function glob_recursive($pattern, $flags = 0) {
    $files = glob($pattern, $flags);
    foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
        $files = array_merge($files, glob_recursive($dir . '/' . basename($pattern), $flags));
    }
    return $files;
}

