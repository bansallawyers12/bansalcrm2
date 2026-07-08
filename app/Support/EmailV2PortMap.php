<?php

namespace App\Support;

/**
 * BansalLaw_CRM → bansalcrm2 email-v2 port reference (Phase 0 prep).
 *
 * Shared by client detail and partner detail Emails tabs (same emails_v2 stack).
 * Used as the source-of-truth map for upcoming port phases — no runtime side effects.
 */
final class EmailV2PortMap
{
    /** @var array<string, string> BansalLaw path/key => bansalcrm2 target path */
    public const FILE_MAP = [
        'app/Http/Controllers/CRM/EmailUploadController.php' => 'app/Http/Controllers/CRM/EmailUploadV2Controller.php',
        'app/Http/Controllers/CRM/ClientsController.php (fetch/delete/preview)' => 'app/Http/Controllers/CRM/EmailQueryV2Controller.php (+ dedicated delete in Phase 5)',
        'app/Http/Controllers/CRM/EmailLogAttachmentController.php' => 'app/Http/Controllers/CRM/MailReportAttachmentController.php',
        'public/js/outlook_emails.js' => 'public/js/emails_v2.js',
        'resources/views/crm/emails_outlook.blade.php' => 'resources/views/Admin/clients/tabs/emails_v2.blade.php',
        'python_services/services/email_parser_service.py' => 'python_services/services/email_parser_service.py',
        'python_services/services/email_renderer_service.py' => 'python_services/services/email_renderer_service.py',
        'python_services/main.py' => 'python_services/main.py',
    ];

    /** @var array<string, string> BansalLaw method → port into bansalcrm2 */
    public const METHOD_MAP = [
        'EmailUploadController::findExistingEmailLog' => 'EmailUploadV2Controller::findExistingEmail (Phase 2)',
        'EmailUploadController::saveEmailPdfDocument' => 'EmailUploadV2Controller::saveEmailPdfDocument (Phase 4)',
        'EmailUploadController::previewEmailAttachments' => 'EmailUploadV2Controller::previewEmailAttachments (Phase 3, optional)',
        'EmailUploadController::saveEmailAttachmentAsDocument' => 'EmailUploadV2Controller — client only (Phase 3, optional)',
        'ClientsController::deleteEmailLog' => 'EmailQueryV2Controller::deleteEmail (Phase 5)',
        'ClientsController::getParsedEmailHtml' => 'EmailQueryV2Controller::getParsedEmailHtml (Phase 4)',
        'ClientsController::resolveEmailPdfPreviewUrl' => 'EmailQueryV2Controller helper (Phase 4)',
        'email_parser_service::parse_eml_file' => 'email_parser_service (Phase 1)',
        'email_renderer_service::render_to_pdf' => 'email_renderer_service (Phase 4)',
        'outlook_emails.js::renderHtmlIframe' => 'emails_v2.js (Phase 4)',
        'outlook_emails.js::showDuplicateEmailPrompt' => 'emails_v2.js (Phase 2)',
    ];

    /** Baseline features to re-test after each phase (client + partner Emails tab). */
    public const BASELINE_CHECKLIST = [
        'upload_msg_inbox',
        'upload_msg_sent',
        'list_inbox_sent',
        'open_email_preview',
        'attachments_download_preview_zip',
        'labels_apply_filter_remove',
        'reply_forward',
        'delete_admin',
        'client_college_subtabs',
        'partner_no_college_tabs',
    ];

    public static function bansalLawRoot(): string
    {
        return (string) env('BANSALLAW_CRM_ROOT', 'C:\\xampp\\htdocs\\BansalLaw_CRM');
    }

    /**
     * @return array<int, array{bansal_law: string, bansalcrm2: string, exists_source: bool, exists_target: bool}>
     */
    public static function verifyFileMap(): array
    {
        $root = self::bansalLawRoot();
        $results = [];

        foreach (self::FILE_MAP as $source => $target) {
            $sourcePath = str_contains($source, ' ')
                ? null
                : $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $source);
            $targetPath = base_path(str_replace('/', DIRECTORY_SEPARATOR, explode(' ', $target)[0]));

            $results[] = [
                'bansal_law' => $source,
                'bansalcrm2' => $target,
                'exists_source' => $sourcePath === null ? true : is_file($sourcePath),
                'exists_target' => is_file($targetPath),
            ];
        }

        return $results;
    }

    /**
     * Step 0.3 — automated checks that Phase 0 prep did not alter existing email-v2 behaviour.
     *
     * @return array{passed: bool, checks: array<int, array{name: string, passed: bool, detail: string}>}
     */
    public static function verifyPostPrepBaseline(): array
    {
        $checks = [];

        $assert = static function (string $name, bool $passed, string $detail) use (&$checks): void {
            $checks[] = ['name' => $name, 'passed' => $passed, 'detail' => $detail];
        };

        // Config: defaults include msg and eml (Phase 1).
        $exts = config('crm.email_upload_allowed_extensions', []);
        $assert(
            'config_upload_extensions',
            is_array($exts) && count($exts) > 0 && in_array('msg', $exts, true),
            'expected msg in allowed extensions, got [' . implode(', ', $exts) . ']'
        );
        $assert(
            'config_upload_max_kb',
            (int) config('crm.email_upload_max_kb') === 30720,
            'expected 30720, got ' . (int) config('crm.email_upload_max_kb')
        );

        // Existing config keys still load (prep must not break crm.php).
        $assert(
            'config_elite_domain_intact',
            is_string(config('crm.education_elite_sender_domain')),
            'education_elite_sender_domain missing or invalid'
        );

        // Controllers resolve from container (no bootstrap/syntax regression).
        foreach ([
            \App\Http\Controllers\CRM\EmailUploadV2Controller::class,
            \App\Http\Controllers\CRM\EmailQueryV2Controller::class,
            \App\Http\Controllers\CRM\EmailLabelV2Controller::class,
            \App\Http\Controllers\CRM\MailReportAttachmentController::class,
        ] as $controllerClass) {
            $short = class_basename($controllerClass);
            try {
                $instance = app($controllerClass);
                $assert("controller_resolves_{$short}", $instance instanceof $controllerClass, 'ok');
            } catch (\Throwable $e) {
                $assert("controller_resolves_{$short}", false, $e->getMessage());
            }
        }

        $uploadPath = app_path('Http/Controllers/CRM/EmailUploadV2Controller.php');
        $uploadSource = is_file($uploadPath) ? (string) file_get_contents($uploadPath) : '';
        $assert(
            'upload_validation_config_driven',
            str_contains($uploadSource, 'emailUploadValidationRules') && str_contains($uploadSource, 'findExistingEmail'),
            'EmailUploadV2Controller missing config-driven validation or duplicate detection'
        );

        // Blade file input accepts configured extensions.
        $bladePath = resource_path('views/Admin/clients/tabs/emails_v2.blade.php');
        $bladeSource = is_file($bladePath) ? (string) file_get_contents($bladePath) : '';
        $assert(
            'blade_accept_from_config',
            str_contains($bladeSource, '$emailUploadAccept') && str_contains($bladeSource, 'duplicateEmailModalV2'),
            'emails_v2.blade.php missing config accept or duplicate modal'
        );
        $assert(
            'blade_entity_type_client_partner',
            str_contains($bladeSource, 'data-entity-type="{{ $entityType }}"'),
            'entity-type data attribute missing'
        );

        // JS entry points still present.
        $jsPath = public_path('js/emails_v2.js');
        $jsSource = is_file($jsPath) ? (string) file_get_contents($jsPath) : '';
        foreach (['loadEmailDetail', 'handleDeleteEmail', 'showDuplicateEmailPrompt', 'getAllowedEmailExtensions', '/email-v2/upload-inbox'] as $needle) {
            $assert(
                'js_contains_' . preg_replace('/[^a-z0-9_]+/i', '_', $needle),
                str_contains($jsSource, $needle),
                "emails_v2.js missing: {$needle}"
            );
        }

        // Client + partner detail pages still include shared emails tab.
        $clientDetail = (string) file_get_contents(resource_path('views/Admin/clients/detail.blade.php'));
        $partnerDetail = (string) file_get_contents(resource_path('views/Admin/partners/detail.blade.php'));
        $assert(
            'client_detail_includes_emails_v2',
            str_contains($clientDetail, "Admin.clients.tabs.emails_v2"),
            'client detail missing emails_v2 include'
        );
        $assert(
            'partner_detail_includes_emails_v2',
            str_contains($partnerDetail, "Admin.clients.tabs.emails_v2"),
            'partner detail missing emails_v2 include'
        );

        // Email model metadata columns used by upload pipeline still declared.
        $emailFillable = (new \App\Models\Email())->getFillable();
        foreach (['message_id', 'file_hash'] as $column) {
            $assert(
                "email_model_{$column}",
                in_array($column, $emailFillable, true),
                "Email model missing fillable: {$column}"
            );
        }

        // checkPythonService callable (structure only — service may be offline).
        try {
            $controller = app(\App\Http\Controllers\CRM\EmailUploadV2Controller::class);
            $serviceCheck = $controller->checkPythonService();
            $assert(
                'check_python_service_structure',
                is_array($serviceCheck) && array_key_exists('status', $serviceCheck) && array_key_exists('url', $serviceCheck),
                'checkPythonService() return shape changed'
            );
        } catch (\Throwable $e) {
            $assert('check_python_service_structure', false, $e->getMessage());
        }

        // Phase 3–4: attachment preview, PDF pipeline, Outlook-style reading pane.
        $assert(
            'upload_preview_attachments_method',
            str_contains($uploadSource, 'previewEmailAttachments') && str_contains($uploadSource, 'saveEmailPdfDocument'),
            'EmailUploadV2Controller missing Phase 3/4 upload methods'
        );
        $assert(
            'email_model_pdf_doc_id',
            in_array('pdf_doc_id', $emailFillable, true),
            'Email model missing fillable: pdf_doc_id'
        );

        $queryPath = app_path('Http/Controllers/CRM/EmailQueryV2Controller.php');
        $querySource = is_file($queryPath) ? (string) file_get_contents($queryPath) : '';
        $assert(
            'query_pdf_preview_urls',
            str_contains($querySource, 'pdf_preview_url') && str_contains($querySource, 'msg_file_url'),
            'EmailQueryV2Controller missing PDF/msg URL fields'
        );
        $assert(
            'query_delete_email_method',
            str_contains($querySource, 'deleteEmail') && str_contains($querySource, 'email_label_mail_report'),
            'EmailQueryV2Controller missing dedicated deleteEmail (Phase 5)'
        );

        foreach (['renderHtmlIframe', 'collectEmailAttachmentItems', 'previewEmailAttachments', '/email-v2/preview-attachments'] as $needle) {
            $assert(
                'js_phase34_' . preg_replace('/[^a-z0-9_]+/i', '_', $needle),
                str_contains($jsSource, $needle),
                "emails_v2.js missing Phase 3/4: {$needle}"
            );
        }

        $assert(
            'js_phase5_delete_endpoint',
            str_contains($jsSource, 'handleDeleteEmail') && str_contains($jsSource, '/email-v2/') && str_contains($jsSource, '/delete'),
            'emails_v2.js missing Phase 5 dedicated delete route'
        );

        $rendererPath = base_path('python_services/services/email_renderer_service.py');
        $rendererSource = is_file($rendererPath) ? (string) file_get_contents($rendererPath) : '';
        $mainPySource = is_file(base_path('python_services/main.py')) ? (string) file_get_contents(base_path('python_services/main.py')) : '';
        $assert(
            'python_render_to_pdf',
            str_contains($rendererSource, 'def render_to_pdf'),
            'email_renderer_service.py missing render_to_pdf'
        );
        $assert(
            'python_parse_render_pdf_route',
            str_contains($mainPySource, '/email/parse-render-pdf'),
            'main.py missing /email/parse-render-pdf endpoint'
        );

        $passed = ! in_array(false, array_column($checks, 'passed'), true);

        return ['passed' => $passed, 'checks' => $checks];
    }
}
