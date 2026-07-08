<?php

/**
 * Phase 6 — Email V2 final regression (automated code + asset checks).
 *
 * Usage: php scripts/email-v2-phase6-regression.php
 *
 * Complements manual UI testing on client + partner Emails tabs.
 * Exit 0 = all automated checks pass.
 */

declare(strict_types=1);

$projectRoot = dirname(__DIR__);
require $projectRoot . '/vendor/autoload.php';

$app = require $projectRoot . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Support\EmailV2PortMap;
use Illuminate\Support\Facades\Route;

$checks = [];
$assert = static function (string $id, string $label, bool $passed, string $detail = '') use (&$checks): void {
    $checks[] = [
        'id' => $id,
        'label' => $label,
        'passed' => $passed,
        'detail' => $detail,
    ];
};

echo '=== Email V2 Phase 6 Regression (automated) ===' . PHP_EOL . PHP_EOL;

// Reuse Phase 0–5 baseline
$baseline = EmailV2PortMap::verifyPostPrepBaseline();
foreach ($baseline['checks'] as $row) {
    $assert('baseline_' . $row['name'], 'Baseline: ' . $row['name'], $row['passed'], $row['detail'] ?? '');
}

$jsPath = public_path('js/emails_v2.js');
$js = is_file($jsPath) ? (string) file_get_contents($jsPath) : '';
$bladePath = resource_path('views/Admin/clients/tabs/emails_v2.blade.php');
$blade = is_file($bladePath) ? (string) file_get_contents($bladePath) : '';
$uploadPath = app_path('Http/Controllers/CRM/EmailUploadV2Controller.php');
$upload = is_file($uploadPath) ? (string) file_get_contents($uploadPath) : '';
$queryPath = app_path('Http/Controllers/CRM/EmailQueryV2Controller.php');
$query = is_file($queryPath) ? (string) file_get_contents($queryPath) : '';

// #1–2 Upload .msg / .eml inbox + sent
$exts = config('crm.email_upload_allowed_extensions', []);
$assert('p6_upload_msg_eml', '#1–2 Upload .msg + .eml (config)', in_array('msg', $exts, true) && in_array('eml', $exts, true));
$assert('p6_upload_inbox_sent_routes', '#1–2 Inbox + sent upload routes', Route::has('email-v2.upload.inbox') && Route::has('email-v2.upload.sent'));
$assert('p6_upload_accept_blade', '#1–2 Dynamic accept in blade', str_contains($blade, '$emailUploadAccept'));

// #3 Duplicate detect + force upload
$assert('p6_duplicate_detect', '#3 Duplicate detection', str_contains($upload, 'findExistingEmail') && str_contains($upload, 'force_upload'));
$assert('p6_duplicate_ui', '#3 Duplicate modal + re-upload', str_contains($js, 'showDuplicateEmailPrompt') && str_contains($js, 'force_upload'));

// #4 HTML preview + inline images
$assert('p6_html_iframe', '#4 HTML iframe preview', str_contains($js, 'renderHtmlIframe') && str_contains($js, 'renderEmailBodyInIframe'));
$assert('p6_cid_replace', '#4 Inline image CID replace', str_contains($js, 'replaceCidReferences'));

// #5 Old emails (pre-migration)
$assert('p6_legacy_attachments', '#5 Legacy attachment fallback', str_contains($query, 'legacyAttachmentsFromJson'));
$assert('p6_optional_pdf_urls', '#5 Optional PDF/msg URLs (old rows)', str_contains($query, 'appendEmailPreviewFields'));

// #6 Attachments download / preview / ZIP
$assert('p6_attachment_routes', '#6 Attachment download/preview/zip routes',
    Route::has('email-v2.attachments.download')
    && Route::has('email-v2.attachments.preview')
    && Route::has('email-v2.attachments.download-all')
);
$assert('p6_attachment_js', '#6 Attachment handlers in JS',
    str_contains($js, 'downloadAllAttachments') && str_contains($js, 'openAttachmentPreviewInNewTab')
);

// #7 Labels apply, filter, remove
$assert('p6_label_routes', '#7 Label routes', Route::has('email-v2.labels.apply') && Route::has('email-v2.labels.remove'));
$assert('p6_label_js', '#7 Label apply/filter/remove JS', str_contains($js, 'applyLabel') && str_contains($js, 'removeLabel') && str_contains($js, 'labelV2Filter'));

// #8 Reply / Forward
$assert('p6_reply_forward', '#8 Reply + Forward', str_contains($js, 'handleReply') && str_contains($js, 'handleForward'));

// #9 Client vs College sub-tab
$assert('p6_college_tabs', '#9 Client/College sub-tabs', str_contains($js, 'showEmailCategoryTabs') && str_contains($js, 'currentEmailCategory'));
$assert('p6_partner_no_college', '#9 Partner skips college tabs', str_contains($blade, 'data-show-email-category'));

// #10 Delete email (admin)
$assert('p6_delete_route', '#10 Dedicated delete route', Route::has('email-v2.delete'));
$assert('p6_delete_js', '#10 Delete wired to email-v2', str_contains($js, '/delete') && str_contains($query, 'deleteEmail'));

// #11 Python service down — graceful handling
$assert('p6_check_service_route', '#11 check-service route', Route::has('email-v2.check.service'));
$assert('p6_upload_error_handling', '#11 Upload error handling (no throw to page)', str_contains($js, 'Upload failed') && str_contains($js, 'Python service'));
$assert('p6_list_error_state', '#11 List load empty state on error', str_contains($js, 'renderEmptyState'));
$assert('p6_python_status_banner', '#11 Python status banner on tab', str_contains($js, 'checkEmailPythonServiceStatus'));

// #12 npm build / assets
$manifestPath = public_path('build/manifest.json');
$manifestOk = is_file($manifestPath);
$manifest = $manifestOk ? json_decode((string) file_get_contents($manifestPath), true) : null;
$entryKey = 'resources/js/pages/admin/emails-v2-entry.js';
$builtRel = is_array($manifest) && isset($manifest[$entryKey]['file']) ? (string) $manifest[$entryKey]['file'] : '';
$builtPath = $builtRel !== '' ? public_path('build/' . ltrim($builtRel, '/')) : '';
$builtSource = is_file($builtPath) ? (string) file_get_contents($builtPath) : '';

$assert('p6_vite_manifest', '#12 Vite manifest exists', $manifestOk);
$assert('p6_vite_emails_entry', '#12 emails-v2-entry in manifest', $builtRel !== '');

$sourceMarkers = [
    '/email-v2/filter-emails',
    '/email-v2/preview-attachments',
    '/email-v2/check-service',
    '/delete',
    'Original email file',
    'Python service',
];
$builtHasMarkers = $builtSource !== '';
if ($builtHasMarkers) {
    foreach ($sourceMarkers as $marker) {
        if (! str_contains($builtSource, $marker)) {
            $builtHasMarkers = false;
            break;
        }
    }
}
$assert(
    'p6_built_assets_fresh',
    '#12 Built bundle includes Phase 3–5 markers (run npm run build if FAIL)',
    $builtHasMarkers,
    $builtRel !== '' ? 'bundle: ' . $builtRel : 'missing built file'
);

// Partner detail includes shared view
$partnerDetail = (string) file_get_contents(resource_path('views/Admin/partners/detail.blade.php'));
$assert('p6_partner_emails_tab', 'Partner detail uses shared emails_v2', str_contains($partnerDetail, 'Admin.clients.tabs.emails_v2'));

echo 'Checklist (automated):' . PHP_EOL;
$failed = 0;
foreach ($checks as $row) {
    $icon = $row['passed'] ? 'PASS' : 'FAIL';
    if (! $row['passed']) {
        $failed++;
    }
    $detail = ($row['detail'] ?? '') !== '' ? ' — ' . $row['detail'] : '';
    echo sprintf('  [%s] %s%s', $icon, $row['label'], $detail) . PHP_EOL;
}

echo PHP_EOL . 'Manual UI (client + partner Emails tab):' . PHP_EOL;
$manual = [
    'Upload .msg inbox + sent',
    'Upload .eml inbox + sent',
    'Duplicate prompt + force upload',
    'Open email — HTML + inline images',
    'Open pre-migration email',
    'Attachment download / preview / ZIP',
    'Labels apply, filter, remove',
    'Reply / Forward',
    'Client vs College sub-tab (client only)',
    'Delete email as super-admin',
    'Stop Python service → browse still works, upload shows error',
    'Hard-refresh after deploy — assets load',
];
foreach ($manual as $i => $item) {
    echo '  [ ] ' . ($i + 1) . '. ' . $item . PHP_EOL;
}

echo PHP_EOL;
if ($failed > 0) {
    echo "=== Phase 6 regression: FAIL ({$failed} automated check(s)) ===" . PHP_EOL;
    exit(1);
}

echo '=== Phase 6 regression: PASS (automated) — complete manual checklist above ===' . PHP_EOL;
exit(0);
