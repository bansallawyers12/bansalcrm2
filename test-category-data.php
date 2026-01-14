<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing Category Model...\n";
echo "================================\n\n";

try {
    $categories = \App\Models\Category::all();
    echo "Category count: " . $categories->count() . "\n\n";
    
    if ($categories->count() > 0) {
        echo "Categories:\n";
        foreach($categories as $cat) {
            echo "  ID: {$cat->id} | Name: {$cat->category_name}\n";
        }
    } else {
        echo "NO CATEGORIES FOUND IN DATABASE!\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
