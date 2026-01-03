<?php
/**
 * Script to fix admin/ URL references across all blade files
 * 
 * This script replaces admin/ with / in regular admin routes
 * and admin/ with adminconsole/ in AdminConsole routes
 * 
 * Usage: php fix_all_admin_urls.php
 */

$viewsPath = __DIR__ . '/resources/views';

if (!is_dir($viewsPath)) {
    die("Error: Views directory not found at: $viewsPath\n");
}

// Patterns to replace for regular Admin views (admin/ -> /)
$adminReplacements = [
    // URL::to patterns
    "URL::to('/admin/" => "URL::to('/",
    "URL::to('admin/" => "URL::to('",
    'URL::to("admin/' => 'URL::to("',
    
    // url() helper patterns
    "url('admin/" => "url('",
    'url("admin/' => 'url("',
    
    // Form::open patterns
    "'url' => 'admin/" => "'url' => '",
    '"url" => "admin/' => '"url" => "',
    
    // fetch() patterns
    "fetch('/admin/" => "fetch('/",
    'fetch("/admin/' => 'fetch("/',
    
    // site_url patterns
    "site_url+'/admin/" => "site_url+'/",
    'site_url+"/admin/' => 'site_url+"/',
    
    // action patterns in forms
    'action="{{ url(\'admin/' => 'action="{{ url(\'',
    "action=\"{{ url('admin/" => "action=\"{{ url('",
];

// Patterns to replace for AdminConsole views (admin/ -> adminconsole/)
$adminConsoleReplacements = [
    // Form::open patterns
    "'url' => 'admin/" => "'url' => 'adminconsole/",
    '"url" => "admin/' => '"url" => "adminconsole/',
    
    // URL::to patterns
    "URL::to('admin/" => "URL::to('adminconsole/",
    'URL::to("admin/' => 'URL::to("adminconsole/',
    "URL::to('/admin/" => "URL::to('/adminconsole/",
    
    // url() helper patterns
    "url('admin/" => "url('adminconsole/",
    'url("admin/' => 'url("adminconsole/',
    
    // fetch() patterns
    "fetch('/admin/" => "fetch('/adminconsole/",
    'fetch("/admin/' => 'fetch("/adminconsole/',
    
    // site_url patterns
    "site_url+'/admin/" => "site_url+'/adminconsole/",
    'site_url+"/admin/' => 'site_url+"/adminconsole/',
];

// Exclude login routes from replacement
$excludePatterns = [
    'admin/login',
    'admin/logout',
    '/admin"',
    "'admin'",
    '"admin"',
];

function shouldExclude($content, $line) {
    global $excludePatterns;
    foreach ($excludePatterns as $pattern) {
        if (strpos($line, $pattern) !== false) {
            return true;
        }
    }
    return false;
}

// Recursively find all blade files
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($viewsPath),
    RecursiveIteratorIterator::SELF_FIRST
);

$filesProcessed = 0;
$filesModified = 0;
$totalReplacements = 0;

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $filePath = $file->getRealPath();
        $relativePath = str_replace(__DIR__ . '/', '', $filePath);
        $filesProcessed++;
        
        // Determine which replacements to use
        $isAdminConsole = strpos($relativePath, 'AdminConsole') !== false;
        $replacements = $isAdminConsole ? $adminConsoleReplacements : $adminReplacements;
        
        $content = file_get_contents($filePath);
        $originalContent = $content;
        $fileReplacements = 0;
        
        // Apply all replacements line by line to avoid breaking login routes
        $lines = explode("\n", $content);
        $modifiedLines = [];
        
        foreach ($lines as $line) {
            $originalLine = $line;
            
            // Skip if line contains excluded patterns
            if (!shouldExclude($content, $line)) {
                foreach ($replacements as $old => $new) {
                    $line = str_replace($old, $new, $line);
                }
            }
            
            if ($line !== $originalLine) {
                $fileReplacements++;
            }
            
            $modifiedLines[] = $line;
        }
        
        $content = implode("\n", $modifiedLines);
        
        // Only write if content changed
        if ($content !== $originalContent) {
            file_put_contents($filePath, $content);
            $filesModified++;
            $totalReplacements += $fileReplacements;
            $type = $isAdminConsole ? '[AdminConsole]' : '[Admin]';
            echo "✓ $type Modified: $relativePath ($fileReplacements replacements)\n";
        }
    }
}

echo "\n";
echo "========================================\n";
echo "Summary:\n";
echo "========================================\n";
echo "Files processed: $filesProcessed\n";
echo "Files modified: $filesModified\n";
echo "Total replacements: $totalReplacements\n";
echo "========================================\n";

