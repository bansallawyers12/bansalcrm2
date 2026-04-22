<?php
/**
 * TEMPORARY deploy helper — DELETE THIS FILE after use.
 * Visit: https://bansalcrm.com/deploy-helper.php?token=elite2026
 */
define('DEPLOY_TOKEN', 'elite2026');

if (($_GET['token'] ?? '') !== DEPLOY_TOKEN) {
    http_response_code(403);
    die('Forbidden');
}

$root = dirname(__DIR__);
$out  = [];

// 1. Git pull
exec('cd ' . escapeshellarg($root) . ' && git pull origin master 2>&1', $out);

// 2. Clear caches via artisan
$cmds = [
    'view:clear',
    'config:cache',
    'route:cache',
    'cache:clear',
];
foreach ($cmds as $cmd) {
    exec('cd ' . escapeshellarg($root) . ' && php artisan ' . $cmd . ' 2>&1', $out);
}

echo '<pre style="font:14px monospace;padding:20px;">';
echo '<strong>Deploy output:</strong>' . PHP_EOL . PHP_EOL;
echo htmlspecialchars(implode(PHP_EOL, $out));
echo PHP_EOL . PHP_EOL . '<strong style="color:green;">Done! Delete this file: public/deploy-helper.php</strong>';
echo '</pre>';
