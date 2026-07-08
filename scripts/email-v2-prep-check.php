<?php

/**
 * Phase 0 — email-v2 port prep verification.
 *
 * Usage: php scripts/email-v2-prep-check.php
 *
 * Checks file mapping, email-v2 routes, and Python microservice health.
 * Does not modify data or configuration.
 */

declare(strict_types=1);

$projectRoot = dirname(__DIR__);
require $projectRoot . '/vendor/autoload.php';

$app = require $projectRoot . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Support\EmailV2PortMap;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

echo "=== Email V2 Phase 0 Prep Check ===" . PHP_EOL . PHP_EOL;

// 1. File map
echo "1. BansalLaw → bansalcrm2 file map" . PHP_EOL;
$mapOk = true;
foreach (EmailV2PortMap::verifyFileMap() as $row) {
    $src = $row['exists_source'] ? 'OK' : 'MISSING';
    $tgt = $row['exists_target'] ? 'OK' : 'MISSING';
    if (! $row['exists_source'] || ! $row['exists_target']) {
        $mapOk = false;
    }
    echo "   [src:$src] [tgt:$tgt] {$row['bansal_law']}" . PHP_EOL;
    echo "              → {$row['bansalcrm2']}" . PHP_EOL;
}
echo $mapOk ? "   File map: PASS" . PHP_EOL : "   File map: FAIL (see above)" . PHP_EOL;
echo PHP_EOL;

// 2. Shared view (client + partner)
echo "2. Shared Emails tab view (client + partner)" . PHP_EOL;
$blade = resource_path('views/Admin/clients/tabs/emails_v2.blade.php');
echo '   emails_v2.blade.php: ' . (is_file($blade) ? 'OK' : 'MISSING') . PHP_EOL;
echo '   entity-type: client | partner via data-entity-type' . PHP_EOL;
echo '   partner includes same view: Admin/partners/detail.blade.php @include emails_v2' . PHP_EOL;
echo PHP_EOL;

// 3. email-v2 routes
echo "3. email-v2 routes (routes/clients.php)" . PHP_EOL;
$expectedRoutes = [
    'email-v2.upload.inbox',
    'email-v2.upload.sent',
    'email-v2.preview.attachments',
    'email-v2.preview.html',
    'email-v2.delete',
    'email-v2.check.service',
    'email-v2.filter.emails',
    'email-v2.filter.sentemails',
    'email-v2.labels.index',
    'email-v2.labels.apply',
    'email-v2.attachments.preview',
    'email-v2.attachments.download',
    'email-v2.attachments.download-all',
];
$routesOk = true;
foreach ($expectedRoutes as $name) {
    $exists = Route::has($name);
    if (! $exists) {
        $routesOk = false;
    }
    echo '   ' . ($exists ? 'OK' : 'MISSING') . "  {$name}" . PHP_EOL;
}
echo $routesOk ? "   Routes: PASS" . PHP_EOL : "   Routes: FAIL" . PHP_EOL;
echo '   Phase 6 script: php scripts/email-v2-phase6-regression.php' . PHP_EOL;
echo PHP_EOL;

// 4. Config (Phase 0 defaults — .msg only until Phase 1)
echo "4. Upload config (config/crm.php)" . PHP_EOL;
$exts = config('crm.email_upload_allowed_extensions', []);
$maxKb = config('crm.email_upload_max_kb', 0);
echo '   allowed_extensions: ' . implode(', ', $exts) . PHP_EOL;
echo '   max_kb: ' . $maxKb . PHP_EOL;
echo PHP_EOL;

// 5. Python service
echo "5. Python microservice health" . PHP_EOL;
$pythonUrl = rtrim((string) config('services.python.url', env('PYTHON_SERVICE_URL', 'http://localhost:5001')), '/');
echo "   URL: {$pythonUrl}" . PHP_EOL;
try {
    $response = Http::timeout(5)->get($pythonUrl . '/health');
    if ($response->successful()) {
        $body = $response->json();
        echo '   Status: UP' . PHP_EOL;
        if (is_array($body)) {
            echo '   Response: ' . json_encode($body, JSON_UNESCAPED_SLASHES) . PHP_EOL;
        }
    } else {
        echo '   Status: DOWN (HTTP ' . $response->status() . ')' . PHP_EOL;
    }
} catch (Throwable $e) {
    echo '   Status: DOWN (' . $e->getMessage() . ')' . PHP_EOL;
    echo '   Action: start python_services before Phase 1 upload tests.' . PHP_EOL;
}
echo PHP_EOL;

// 6. Step 0.3 — verify prep did not change existing behaviour
echo "6. Step 0.3 — post-prep baseline (automated)" . PHP_EOL;
$baseline = EmailV2PortMap::verifyPostPrepBaseline();
$baselineOk = $baseline['passed'];
foreach ($baseline['checks'] as $check) {
    $mark = $check['passed'] ? 'PASS' : 'FAIL';
    echo "   [{$mark}] {$check['name']}" . PHP_EOL;
    if (! $check['passed']) {
        echo "          {$check['detail']}" . PHP_EOL;
    }
}
echo $baselineOk
    ? "   Post-prep baseline: PASS (existing email-v2 rules unchanged)" . PHP_EOL
    : "   Post-prep baseline: FAIL" . PHP_EOL;
echo PHP_EOL;

// 7. Baseline checklist (manual UI — optional smoke in browser)
echo "7. Baseline checklist (manual UI — client + partner Emails tab)" . PHP_EOL;
foreach (EmailV2PortMap::BASELINE_CHECKLIST as $item) {
    echo "   [ ] {$item}" . PHP_EOL;
}
echo PHP_EOL;

$exitCode = ($mapOk && $routesOk && $baselineOk) ? 0 : 1;
echo "=== Prep check complete (exit {$exitCode}) ===" . PHP_EOL;
exit($exitCode);
