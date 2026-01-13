<!DOCTYPE html>
<html>
<head>
    <title>Test Master Category Dropdown</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
</head>
<body style="padding: 50px;">
    <h2>Testing Master Category Dropdown</h2>
    
    <label>Master Category:</label>
    <select id="test-select" class="addressselect2" style="width: 300px;">
        <option value="">Select a Master Category</option>
        <?php
        require __DIR__.'/../vendor/autoload.php';
        $app = require_once __DIR__.'/../bootstrap/app.php';
        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
        
        echo "<!-- Categories count: " . \App\Models\Category::count() . " -->\n";
        
        foreach(\App\Models\Category::all() as $clist) {
            echo "<option value='{$clist->id}'>{$clist->category_name}</option>\n";
        }
        ?>
    </select>
    
    <p style="margin-top: 20px;">
        <strong>Debug Info:</strong><br>
        Categories loaded: <?php echo \App\Models\Category::count(); ?>
    </p>
    
    <script>
    jQuery(document).ready(function($){
        console.log('Initializing Select2...');
        $(".addressselect2").select2({
            minimumResultsForSearch: Infinity
        });
        console.log('Select2 initialized');
    });
    </script>
</body>
</html>
