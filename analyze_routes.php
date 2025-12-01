<?php
/**
 * Route Analysis Script - Phase 1 Deep Analysis
 * 
 * This script analyzes routes/web.php to:
 * 1. Extract all route definitions
 * 2. Map controller references
 * 3. Verify controllers exist
 * 4. Generate conversion mapping
 */

$routesFile = __DIR__ . '/routes/web.php';
$agentFile = __DIR__ . '/routes/agent.php';
$controllersPath = __DIR__ . '/app/Http/Controllers';

// Read routes file
$routesContent = file_get_contents($routesFile);
$agentContent = file_get_contents($agentFile);

// Patterns to match routes
$routePatterns = [
    // Route::get/post/put/patch/delete/match/any/resource
    '/Route::(get|post|put|patch|delete|match|any|resource)\s*\([^)]*\)/i',
    // Route::prefix()->group()
    '/Route::prefix\([^)]+\)->group\s*\(/i',
];

// Extract all route definitions
$routes = [];
$issues = [];
$controllerMap = [];

// Function to extract controller from route string
function extractController($routeString) {
    $controller = null;
    $method = null;
    
    // Match old syntax: 'Controller@method' or 'Namespace\Controller@method'
    if (preg_match("/['\"]([^'\"]+Controller)@(\w+)['\"]/", $routeString, $matches)) {
        $controller = $matches[1];
        $method = $matches[2];
    }
    
    // Match new syntax: [Controller::class, 'method']
    if (preg_match("/\[([^,]+)::class,\s*['\"](\w+)['\"]\]/", $routeString, $matches)) {
        $controller = trim($matches[1]);
        $method = $matches[2];
    }
    
    return [$controller, $method];
}

// Function to convert namespace path to file path
function namespaceToPath($namespace) {
    // Remove App\Http\Controllers\ prefix if present
    $namespace = preg_replace('/^App\\\Http\\\Controllers\\\/', '', $namespace);
    
    // Convert namespace separators to directory separators
    $path = str_replace('\\', '/', $namespace);
    
    return __DIR__ . '/app/Http/Controllers/' . $path . '.php';
}

// Function to check if controller exists
function controllerExists($controllerPath) {
    return file_exists($controllerPath);
}

// Function to check if method exists in controller
function methodExistsInController($controllerPath, $method) {
    if (!file_exists($controllerPath)) {
        return false;
    }
    
    $content = file_get_contents($controllerPath);
    return preg_match("/function\s+$method\s*\(/", $content);
}

// Parse routes file line by line
$lines = explode("\n", $routesContent);
$lineNumber = 0;
$inGroup = false;
$currentNamespace = 'App\Http\Controllers';

foreach ($lines as $line) {
    $lineNumber++;
    $line = trim($line);
    
    // Skip comments and empty lines
    if (empty($line) || preg_match('/^\/\//', $line) || preg_match('/^\/\*/', $line)) {
        continue;
    }
    
    // Check for namespace in Route::prefix()->group()
    if (preg_match("/->namespace\(['\"]([^'\"]+)['\"]\)/", $line, $matches)) {
        $currentNamespace = $matches[1];
    }
    
    // Extract route definitions
    if (preg_match("/Route::(get|post|put|patch|delete|match|any|resource)\s*\(/", $line, $methodMatch)) {
        $httpMethod = $methodMatch[1];
        
        // Extract route path
        $path = null;
        if (preg_match("/['\"]([^'\"]+)['\"]/", $line, $pathMatch)) {
            $path = $pathMatch[1];
        }
        
        // Extract controller and method
        list($controller, $controllerMethod) = extractController($line);
        
        if ($controller) {
            // Determine full namespace
            $fullControllerPath = $controller;
            
            // If controller doesn't start with namespace, prepend current namespace
            if (!preg_match('/^App\\\\/', $controller)) {
                if (strpos($controller, '\\') === false) {
                    // Simple controller name, use current namespace
                    $fullControllerPath = $currentNamespace . '\\' . $controller;
                } else {
                    // Has namespace but not full path
                    $fullControllerPath = $currentNamespace . '\\' . $controller;
                }
            }
            
            // Convert to file path
            $controllerFilePath = namespaceToPath($fullControllerPath);
            
            // Check if controller exists
            $exists = controllerExists($controllerFilePath);
            $methodExists = $exists && $controllerMethod ? methodExistsInController($controllerFilePath, $controllerMethod) : null;
            
            $routes[] = [
                'line' => $lineNumber,
                'http_method' => $httpMethod,
                'path' => $path,
                'controller' => $controller,
                'full_namespace' => $fullControllerPath,
                'method' => $controllerMethod,
                'file_path' => $controllerFilePath,
                'controller_exists' => $exists,
                'method_exists' => $methodExists,
                'old_syntax' => preg_match("/['\"][^'\"]+Controller@\w+['\"]/", $line) ? true : false,
                'raw_line' => $line,
            ];
            
            if (!$exists) {
                $issues[] = [
                    'type' => 'missing_controller',
                    'line' => $lineNumber,
                    'controller' => $fullControllerPath,
                    'file_path' => $controllerFilePath,
                    'route' => $line,
                ];
            } elseif ($controllerMethod && !$methodExists) {
                $issues[] = [
                    'type' => 'missing_method',
                    'line' => $lineNumber,
                    'controller' => $fullControllerPath,
                    'method' => $controllerMethod,
                    'file_path' => $controllerFilePath,
                    'route' => $line,
                ];
            }
        }
    }
}

// Generate report
echo "========================================\n";
echo "ROUTE ANALYSIS REPORT - Phase 1\n";
echo "========================================\n\n";

echo "SUMMARY:\n";
echo "--------\n";
echo "Total routes found: " . count($routes) . "\n";
echo "Routes with old syntax: " . count(array_filter($routes, function($r) { return $r['old_syntax']; })) . "\n";
echo "Routes with new syntax: " . count(array_filter($routes, function($r) { return !$r['old_syntax']; })) . "\n";
echo "Missing controllers: " . count(array_filter($issues, function($i) { return $i['type'] === 'missing_controller'; })) . "\n";
echo "Missing methods: " . count(array_filter($issues, function($i) { return $i['type'] === 'missing_method'; })) . "\n\n";

// Group routes by controller
$routesByController = [];
foreach ($routes as $route) {
    $key = $route['full_namespace'];
    if (!isset($routesByController[$key])) {
        $routesByController[$key] = [];
    }
    $routesByController[$key][] = $route;
}

echo "ROUTES BY CONTROLLER:\n";
echo "----------------------\n";
foreach ($routesByController as $controller => $controllerRoutes) {
    echo "\n$controller (" . count($controllerRoutes) . " routes):\n";
    foreach ($controllerRoutes as $route) {
        $status = $route['controller_exists'] ? ($route['method_exists'] !== false ? '✓' : '⚠') : '✗';
        echo "  $status Line {$route['line']}: {$route['http_method']} {$route['path']} -> {$route['method']}\n";
    }
}

if (!empty($issues)) {
    echo "\n\nISSUES FOUND:\n";
    echo "-------------\n";
    foreach ($issues as $issue) {
        echo "\n[{$issue['type']}] Line {$issue['line']}:\n";
        echo "  Route: {$issue['route']}\n";
        if ($issue['type'] === 'missing_controller') {
            echo "  Controller: {$issue['controller']}\n";
            echo "  Expected file: {$issue['file_path']}\n";
        } elseif ($issue['type'] === 'missing_method') {
            echo "  Controller: {$issue['controller']}\n";
            echo "  Method: {$issue['method']}\n";
            echo "  File: {$issue['file_path']}\n";
        }
    }
}

// Generate conversion mapping
echo "\n\nCONVERSION MAPPING (Sample - First 10 routes with old syntax):\n";
echo "------------------------------------------------------------\n";
$oldSyntaxRoutes = array_filter($routes, function($r) { return $r['old_syntax']; });
$sample = array_slice($oldSyntaxRoutes, 0, 10);
foreach ($sample as $route) {
    echo "\nLine {$route['line']}:\n";
    echo "  OLD: {$route['raw_line']}\n";
    
    // Generate new syntax
    $newSyntax = "Route::{$route['http_method']}('{$route['path']}', ";
    $newSyntax .= "[{$route['full_namespace']}::class, '{$route['method']}']";
    
    // Check if route has name
    if (preg_match("/->name\(['\"]([^'\"]+)['\"]\)/", $route['raw_line'], $nameMatch)) {
        $newSyntax .= "->name('{$nameMatch[1]}')";
    }
    
    // Check for middleware
    if (preg_match("/->middleware\(['\"]([^'\"]+)['\"]\)/", $route['raw_line'], $middlewareMatch)) {
        $newSyntax .= "->middleware('{$middlewareMatch[1]}')";
    }
    
    $newSyntax .= ");";
    
    echo "  NEW: $newSyntax\n";
}

// Save detailed report to file
$reportFile = __DIR__ . '/route_analysis_report.json';
file_put_contents($reportFile, json_encode([
    'summary' => [
        'total_routes' => count($routes),
        'old_syntax_count' => count(array_filter($routes, function($r) { return $r['old_syntax']; })),
        'new_syntax_count' => count(array_filter($routes, function($r) { return !$r['old_syntax']; })),
        'missing_controllers' => count(array_filter($issues, function($i) { return $i['type'] === 'missing_controller'; })),
        'missing_methods' => count(array_filter($issues, function($i) { return $i['type'] === 'missing_method'; })),
    ],
    'routes' => $routes,
    'issues' => $issues,
    'routes_by_controller' => $routesByController,
], JSON_PRETTY_PRINT));

echo "\n\nDetailed report saved to: $reportFile\n";


