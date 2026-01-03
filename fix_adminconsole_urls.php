<?php
/**
 * Script to fix admin/ URL references to adminconsole/ in AdminConsole blade files
 * 
 * Usage: php fix_adminconsole_urls.php
 */

$adminConsolePath = __DIR__ . '/resources/views/AdminConsole';

if (!is_dir($adminConsolePath)) {
    die("Error: AdminConsole directory not found at: $adminConsolePath\n");
}

// Patterns to replace: old => new
$replacements = [
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

// Recursively find all blade files
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($adminConsolePath),
    RecursiveIteratorIterator::SELF_FIRST
);

$filesProcessed = 0;
$filesModified = 0;
$totalReplacements = 0;

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $filePath = $file->getRealPath();
        $filesProcessed++;
        
        $content = file_get_contents($filePath);
        $originalContent = $content;
        $fileReplacements = 0;
        
        // Apply all replacements
        foreach ($replacements as $old => $new) {
            $count = 0;
            $content = str_replace($old, $new, $content, $count);
            $fileReplacements += $count;
        }
        
        // Only write if content changed
        if ($content !== $originalContent) {
            file_put_contents($filePath, $content);
            $filesModified++;
            $totalReplacements += $fileReplacements;
            echo "✓ Modified: " . str_replace(__DIR__ . '/', '', $filePath) . " ($fileReplacements replacements)\n";
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

