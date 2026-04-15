<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Email;
use App\Models\MailReportAttachment;
use App\Models\Document;
use App\Models\Admin;
use App\Models\Partner;
use App\Models\Agent;

/**
 * Email Query V2 Controller
 * 
 * Handles email filtering and querying for both clients and partners
 */
class EmailQueryV2Controller extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Filter Inbox emails (supports both clients and partners)
     */
    public function filterEmails(Request $request)
    {
        try {
            $entityId = $request->input('client_id'); // Can be client_id or partner_id
            $entityType = $request->input('type', 'client'); // client, lead, or partner
            $status = $request->input('status');
            $search = $request->input('search');
            $label_id = $request->input('label_id');

            // Build base query
            $query = Email::where('client_id', $entityId)
                ->where('type', $entityType)
                ->where('mail_type', 1);

            // Set conversion_type based on entity type
            if ($entityType === 'partner') {
                $query->where('conversion_type', 'partner_email_fetch')
                      ->where('mail_body_type', 'inbox');
            } else {
                $query->where('conversion_type', 'conversion_email_fetch')
                      ->where('mail_body_type', 'inbox');
                // Client detail: filter by email_category (Client sub-tab = client + NULL, College = college)
                $emailCategory = $request->input('email_category', 'client');
                if ($emailCategory === 'college') {
                    $query->where('email_category', 'college');
                } else {
                    $query->where(function ($q) {
                        $q->where('email_category', 'client')->orWhereNull('email_category');
                    });
                }
            }

            $query->with(['labels', 'attachments'])
                  ->orderBy('created_at', 'DESC');

            // Status filter
            if ($status !== null && $status !== '') {
                if ($status == 1) {
                    $query->where('mail_is_read', 1);
                } elseif ($status == 2) {
                    $query->where(function ($q) {
                        $q->where('mail_is_read', 0)
                          ->orWhereNull('mail_is_read');
                    });
                }
            }

            // Search filter
            if ($search !== null && $search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('subject', 'LIKE', "%{$search}%")
                      ->orWhere('message', 'LIKE', "%{$search}%")
                      ->orWhere('from_mail', 'LIKE', "%{$search}%")
                      ->orWhere('to_mail', 'LIKE', "%{$search}%");
                });
            }

            // Label filter
            if (!empty($label_id)) {
                $query->whereHas('labels', function ($q) use ($label_id) {
                    $q->where('email_labels.id', $label_id);
                });
            }

            $emails = $query->get();
            $url = 'https://' . env('AWS_BUCKET') . '.s3.' . env('AWS_DEFAULT_REGION') . '.amazonaws.com/';

            // Get unique ID for S3 path
            $uniqueId = '';
            if ($entityType === 'partner') {
                $partnerInfo = Partner::select('id')->where('id', $entityId)->first();
                $uniqueId = !empty($partnerInfo) ? (string)$partnerInfo->id : '';
            } else {
                $adminInfo = Admin::select('client_id')->where('id', $entityId)->first();
                $uniqueId = !empty($adminInfo) ? $adminInfo->client_id : '';
            }

            $emails = $emails->map(function ($email) use ($url, $uniqueId, $entityType) {
                $previewUrl = '';

                if (!empty($email->uploaded_doc_id)) {
                    $DocInfo = Document::select('id','doc_type','myfile','myfile_key','mail_type')
                        ->where('id', $email->uploaded_doc_id)
                        ->first();

                    if ($DocInfo) {
                        if (!empty($DocInfo->myfile_key)) {
                            $previewUrl = $DocInfo->myfile;
                        } else {
                            $docType = ($entityType === 'partner') ? 'partner_email_fetch' : 'conversion_email_fetch';
                            $previewUrl = $url . $uniqueId . '/' . $docType . '/' . ($DocInfo->mail_type ?? 'inbox') . '/' . $DocInfo->myfile;
                        }
                    }
                }

                // Ensure attachments and labels relationships are loaded
                if (!$email->relationLoaded('attachments')) {
                    $email->load('attachments');
                }
                if (!$email->relationLoaded('labels')) {
                    $email->load('labels');
                }

                // Convert to array
                $emailArray = $email->toArray();
                
                // Fetch attachments
                $attachments = $email->attachments;
                if (!$attachments || (method_exists($attachments, 'count') && $attachments->count() === 0)) {
                    $attachments = MailReportAttachment::where('mail_report_id', $email->id)->get();
                }

                $legacyAttachmentItems = $this->parseEmailLegacyAttachmentItems($email);
                
                // Format attachments
                if ($attachments && method_exists($attachments, 'count') && $attachments->count() > 0) {
                    $emailArray['attachments'] = $attachments->map(function ($attachment) use ($email, $legacyAttachmentItems) {
                        $fileSize = $this->attachmentFileSizeWithLegacyFallback($email, $attachment, $legacyAttachmentItems);
                        return [
                            'id' => $attachment->id,
                            'mail_report_id' => $attachment->mail_report_id,
                            'filename' => $attachment->filename,
                            'display_name' => $attachment->display_name ?? $attachment->filename,
                            'content_type' => $attachment->content_type,
                            'file_path' => $attachment->file_path,
                            's3_key' => $attachment->s3_key,
                            'file_size' => $fileSize,
                            'content_id' => $attachment->content_id,
                            'is_inline' => (bool) $attachment->is_inline,
                            'description' => $attachment->description,
                            'extension' => $attachment->extension,
                        ];
                    })->values()->toArray();
                } else {
                    // Fallback for older emails that stored attachments in the JSON column
                    $emailArray['attachments'] = $this->legacyAttachmentsFromJson($email);
                }
                
                // Format labels explicitly
                $labels = $email->labels;
                if ($labels && method_exists($labels, 'count') && $labels->count() > 0) {
                    $emailArray['labels'] = $labels->map(function ($label) {
                        return [
                            'id' => $label->id,
                            'name' => $label->name,
                            'color' => $label->color,
                            'icon' => $label->icon,
                            'type' => $label->type,
                            'description' => $label->description,
                        ];
                    })->values()->toArray();
                } else {
                    $emailArray['labels'] = [];
                }
                
                $emailArray['preview_url'] = $previewUrl;
                $emailArray['to_mail'] = $this->resolveToMailDisplay($email->to_mail ?? '', $email->type ?? $entityType);
                
                return $emailArray;
            });

            return response()->json($emails, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            Log::error('Error in filterEmails V2: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching emails: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Filter Sent emails (supports both clients and partners)
     */
    public function filterSentEmails(Request $request)
    {
        try {
            $entityId = $request->input('client_id'); // Can be client_id or partner_id
            $entityType = $request->input('type', 'client'); // client, lead, or partner
            $status = $request->input('status');
            $search = $request->input('search');
            $label_id = $request->input('label_id');

            // Build base query
            $query = Email::where('client_id', $entityId)
                ->where('type', $entityType)
                ->where('mail_type', 1);

            // Set conversion_type based on entity type
            if ($entityType === 'partner') {
                $query->where(function ($query) {
                    $query->whereNull('conversion_type')
                        ->orWhere(function ($subQuery) {
                            $subQuery->where('conversion_type', 'partner_email_fetch')
                                ->where('mail_body_type', 'sent');
                        });
                });
            } else {
                $query->where(function ($query) {
                    $query->whereNull('conversion_type')
                        ->orWhere(function ($subQuery) {
                            $subQuery->where('conversion_type', 'conversion_email_fetch')
                                ->where('mail_body_type', 'sent');
                        });
                });
                // Client detail: filter by email_category (Client sub-tab = client + NULL, College = college)
                $emailCategory = $request->input('email_category', 'client');
                if ($emailCategory === 'college') {
                    $query->where('email_category', 'college');
                } else {
                    $query->where(function ($q) {
                        $q->where('email_category', 'client')->orWhereNull('email_category');
                    });
                }
            }

            $query->with(['labels', 'attachments'])
                  ->orderBy('created_at', 'DESC');

            // Status filter
            if ($status !== null && $status !== '') {
                if ($status == 1) {
                    $query->where('mail_is_read', 1);
                } elseif ($status == 2) {
                    $query->where(function ($q) {
                        $q->where('mail_is_read', 0)
                          ->orWhereNull('mail_is_read');
                    });
                }
            }

            // Search filter
            if ($search !== null && $search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('subject', 'LIKE', "%{$search}%")
                      ->orWhere('message', 'LIKE', "%{$search}%")
                      ->orWhere('from_mail', 'LIKE', "%{$search}%")
                      ->orWhere('to_mail', 'LIKE', "%{$search}%");
                });
            }

            // Label filter
            if (!empty($label_id)) {
                $query->whereHas('labels', function ($q) use ($label_id) {
                    $q->where('email_labels.id', $label_id);
                });
            }

            $emails = $query->get();
            $url = 'https://' . env('AWS_BUCKET') . '.s3.' . env('AWS_DEFAULT_REGION') . '.amazonaws.com/';

            // Get unique ID for S3 path
            $uniqueId = '';
            if ($entityType === 'partner') {
                $partnerInfo = Partner::select('id')->where('id', $entityId)->first();
                $uniqueId = !empty($partnerInfo) ? (string)$partnerInfo->id : '';
            } else {
                $adminInfo = Admin::select('client_id')->where('id', $entityId)->first();
                $uniqueId = !empty($adminInfo) ? $adminInfo->client_id : '';
            }

            $emails = $emails->map(function ($email) use ($url, $uniqueId, $entityType, $entityId) {
                $previewUrl = '';

                if (!empty($email->uploaded_doc_id)) {
                    $docInfo = Document::select('id', 'doc_type', 'myfile', 'myfile_key', 'mail_type')
                        ->where('id', $email->uploaded_doc_id)
                        ->first();
                    if ($docInfo) {
                        if (!empty($docInfo->myfile_key)) {
                            $previewUrl = $docInfo->myfile;
                        } else {
                            $docType = ($entityType === 'partner') ? 'partner_email_fetch' : ($docInfo->doc_type ?? 'conversion_email_fetch');
                            $clientRef = $uniqueId ?: ($entityType === 'partner' ? ('partner_' . $entityId) : ('client_' . ($email->client_id ?? $entityId)));
                            $previewUrl = $url . $clientRef . '/' . $docType . '/' . ($docInfo->mail_type ?? 'sent') . '/' . ($docInfo->myfile ?? '');
                        }
                    }
                }

                // Ensure attachments and labels relationships are loaded
                if (!$email->relationLoaded('attachments')) {
                    $email->load('attachments');
                }
                if (!$email->relationLoaded('labels')) {
                    $email->load('labels');
                }

                // Convert to array
                $emailArray = $email->toArray();
                
                // Fetch attachments
                $attachments = $email->attachments;
                if (!$attachments || (method_exists($attachments, 'count') && $attachments->count() === 0)) {
                    $attachments = MailReportAttachment::where('mail_report_id', $email->id)->get();
                }

                $legacyAttachmentItems = $this->parseEmailLegacyAttachmentItems($email);
                
                // Format attachments
                if ($attachments && method_exists($attachments, 'count') && $attachments->count() > 0) {
                    $emailArray['attachments'] = $attachments->map(function ($attachment) use ($email, $legacyAttachmentItems) {
                        $fileSize = $this->attachmentFileSizeWithLegacyFallback($email, $attachment, $legacyAttachmentItems);
                        return [
                            'id' => $attachment->id,
                            'mail_report_id' => $attachment->mail_report_id,
                            'filename' => $attachment->filename,
                            'display_name' => $attachment->display_name ?? $attachment->filename,
                            'content_type' => $attachment->content_type,
                            'file_path' => $attachment->file_path,
                            's3_key' => $attachment->s3_key,
                            'file_size' => $fileSize,
                            'content_id' => $attachment->content_id,
                            'is_inline' => (bool) $attachment->is_inline,
                            'description' => $attachment->description,
                            'extension' => $attachment->extension,
                        ];
                    })->values()->toArray();
                } else {
                    // Fallback for older emails that stored attachments in the JSON column
                    $emailArray['attachments'] = $this->legacyAttachmentsFromJson($email);
                }
                
                // Format labels explicitly
                $labels = $email->labels;
                if ($labels && method_exists($labels, 'count') && $labels->count() > 0) {
                    $emailArray['labels'] = $labels->map(function ($label) {
                        return [
                            'id' => $label->id,
                            'name' => $label->name,
                            'color' => $label->color,
                            'icon' => $label->icon,
                            'type' => $label->type,
                            'description' => $label->description,
                        ];
                    })->values()->toArray();
                } else {
                    $emailArray['labels'] = [];
                }
                
                $emailArray['preview_url'] = $previewUrl;
                $emailArray['from_mail'] = $emailArray['from_mail'] ?? '';
                $emailArray['to_mail'] = $this->resolveToMailDisplay($email->to_mail ?? '', $email->type ?? $entityType);
                $emailArray['subject'] = $emailArray['subject'] ?? '';
                $emailArray['message'] = $emailArray['message'] ?? '';
                
                return $emailArray;
            });

            return response()->json($emails, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            Log::error('Error in filterSentEmails V2: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching emails'
            ], 500);
        }
    }

    /**
     * Build attachment list from legacy emails.attachments JSON column.
     * Used when no MailReportAttachment rows exist for older sent emails.
     * Returns entries with id=null so the list is visible but download is not available.
     */
    protected function legacyAttachmentsFromJson(Email $email): array
    {
        $raw = $email->getAttributes()['attachments'] ?? null;
        if (empty($raw)) {
            return [];
        }
        $items = is_string($raw) ? json_decode($raw, true) : $raw;
        if (!is_array($items) || empty($items)) {
            return [];
        }
        return array_values(array_filter(array_map(function ($item) use ($email) {
            $name = $item['file_name'] ?? basename($item['file_url'] ?? '');
            if (empty($name)) {
                return null;
            }
            $fileSize = $this->resolveLegacyAttachmentFileSize($item);
            return [
                'id' => null,
                'mail_report_id' => $email->id,
                'filename' => $name,
                'display_name' => $name,
                'content_type' => 'application/octet-stream',
                'file_path' => $item['file_url'] ?? null,
                's3_key' => null,
                'file_size' => $fileSize,
                'content_id' => null,
                'is_inline' => false,
                'description' => null,
                'extension' => pathinfo($name, PATHINFO_EXTENSION),
            ];
        }, $items)));
    }

    /**
     * Resolve byte size for a legacy JSON attachment: prefer stored file_size, then local filesystem.
     */
    protected function resolveLegacyAttachmentFileSize(array $item): int
    {
        if (isset($item['file_size']) && is_numeric($item['file_size']) && (int) $item['file_size'] > 0) {
            return (int) $item['file_size'];
        }
        $url = $item['file_url'] ?? null;
        if ($url === null || $url === '') {
            return 0;
        }
        if (!is_string($url)) {
            return 0;
        }
        // Remote HTTPS: resolve size from S3 when URL matches our bucket (same as download path)
        if (preg_match('#^https?://#i', $url)) {
            $s3Size = $this->resolveS3ObjectSizeFromUrl($url);
            if ($s3Size > 0) {
                return $s3Size;
            }

            return 0;
        }
        $norm = str_replace('\\', '/', $url);
        $candidates = array_unique(array_filter([
            $url,
            public_path('checklists/' . basename($norm)),
            public_path('checklists/' . ltrim($norm, '/')),
            public_path('img/documents/' . basename($norm)),
        ]));
        foreach ($candidates as $path) {
            if ($path !== '' && @is_file($path)) {
                $sz = @filesize($path);
                if ($sz !== false) {
                    return (int) $sz;
                }
            }
        }
        return 0;
    }

    /**
     * Parsed legacy JSON items from emails.attachments (for size fallback when mail_report_attachments.file_size is 0).
     *
     * @return array<int, array<string, mixed>>
     */
    protected function parseEmailLegacyAttachmentItems(Email $email): array
    {
        $raw = $email->getAttributes()['attachments'] ?? null;
        if (empty($raw)) {
            return [];
        }
        $items = is_string($raw) ? json_decode($raw, true) : $raw;
        return is_array($items) ? $items : [];
    }

    /**
     * Prefer DB file_size; if zero, match legacy JSON by filename and resolve size (stored or filesystem).
     * Finally, resolve from S3 or local path when metadata is missing (matches actual download bytes).
     */
    protected function attachmentFileSizeWithLegacyFallback(Email $email, MailReportAttachment $attachment, array $legacyItems): int
    {
        $size = (int) ($attachment->file_size ?? 0);
        if ($size > 0) {
            return $size;
        }
        if (!empty($legacyItems)) {
            $attrs = $attachment->getAttributes();
            $names = array_unique(array_filter([
                (string) ($attrs['filename'] ?? ''),
                (string) ($attrs['display_name'] ?? ''),
            ]));
            foreach ($legacyItems as $item) {
                if (!is_array($item)) {
                    continue;
                }
                $legacyName = $item['file_name'] ?? basename($item['file_url'] ?? '');
                if ($legacyName === '') {
                    continue;
                }
                foreach ($names as $n) {
                    if ($this->legacyAttachmentNamesMatch($n, $legacyName)) {
                        $resolved = $this->resolveLegacyAttachmentFileSize($item);
                        if ($resolved > 0) {
                            return $resolved;
                        }
                    }
                }
            }
        }

        $fromStorage = $this->resolveMailReportAttachmentSizeFromS3OrLocal($attachment);
        if ($fromStorage > 0) {
            return $fromStorage;
        }

        return 0;
    }

    /**
     * When DB file_size is 0, get object size from S3 (s3_key or URL) or local file_path.
     */
    protected function resolveMailReportAttachmentSizeFromS3OrLocal(MailReportAttachment $attachment): int
    {
        $fp = $attachment->file_path;
        if (is_string($fp) && $fp !== '' && !preg_match('#^https?://#i', $fp)) {
            if (@is_file($fp)) {
                $sz = @filesize($fp);
                if ($sz !== false) {
                    return (int) $sz;
                }
            }
        }

        if (!$this->s3Configured()) {
            return 0;
        }

        try {
            $disk = Storage::disk('s3');
            $key = $attachment->s3_key;
            if (is_string($key) && $key !== '' && $disk->exists($key)) {
                return (int) $disk->size($key);
            }
            $keyFromUrl = $this->extractS3KeyFromPublicUrl($attachment->file_path);
            if (is_string($keyFromUrl) && $keyFromUrl !== '' && $disk->exists($keyFromUrl)) {
                return (int) $disk->size($keyFromUrl);
            }
        } catch (\Exception $e) {
            Log::debug('EmailQueryV2: attachment size from S3 failed', [
                'attachment_id' => $attachment->id,
                'error' => $e->getMessage(),
            ]);
        }

        return 0;
    }

    protected function resolveS3ObjectSizeFromUrl(string $url): int
    {
        if (!$this->s3Configured()) {
            return 0;
        }
        try {
            $key = $this->extractS3KeyFromPublicUrl($url);
            if (!$key) {
                return 0;
            }
            $disk = Storage::disk('s3');
            if ($disk->exists($key)) {
                return (int) $disk->size($key);
            }
        } catch (\Exception $e) {
            // ignore
        }

        return 0;
    }

    protected function s3Configured(): bool
    {
        return !empty(config('filesystems.disks.s3.key')) && !empty(config('filesystems.disks.s3.bucket'));
    }

    /**
     * Derive S3 object key from a public URL (aligned with MailReportAttachmentController).
     */
    protected function extractS3KeyFromPublicUrl(?string $url): ?string
    {
        if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        $configured = rtrim((string) config('filesystems.disks.s3.url'), '/');
        if ($configured !== '' && str_starts_with($url, $configured)) {
            return rawurldecode(ltrim(substr($url, strlen($configured)), '/'));
        }

        $bucket = config('filesystems.disks.s3.bucket');
        $region = (string) config('filesystems.disks.s3.region');
        if (empty($bucket)) {
            return null;
        }

        $bucketQ = preg_quote($bucket, '#');
        $regionQ = preg_quote($region, '#');

        $patterns = [
            "#https?://{$bucketQ}\\.s3\\.{$regionQ}\\.amazonaws\\.com/(.+)#i",
            "#https?://{$bucketQ}\\.s3\\.amazonaws\\.com/(.+)#i",
            "#https?://s3\\.{$regionQ}\\.amazonaws\\.com/{$bucketQ}/(.+)#i",
            "#https?://s3\\.amazonaws\\.com/{$bucketQ}/(.+)#i",
        ];

        foreach ($patterns as $p) {
            if (preg_match($p, $url, $m)) {
                return rawurldecode($m[1]);
            }
        }

        return null;
    }

    protected function legacyAttachmentNamesMatch(string $a, string $b): bool
    {
        $a = trim($a);
        $b = trim($b);
        if ($a === '' || $b === '') {
            return false;
        }
        if (strcasecmp($a, $b) === 0) {
            return true;
        }
        $normalize = function (string $s): string {
            $s = basename(str_replace('\\', '/', $s));
            $s = preg_replace('/\.(pdf|docx?|jpe?g|png|gif|webp)$/i', '', $s);
            $s = preg_replace('/_signed$/i', '', $s);

            return strtolower($s);
        };

        return $normalize($a) === $normalize($b);
    }

    /**
     * Resolve to_mail field: if it contains client/partner/agent IDs, resolve to email addresses.
     */
    protected function resolveToMailDisplay(string $toMail, string $entityType): string
    {
        if (empty(trim($toMail))) {
            return $toMail;
        }
        $parts = array_map('trim', explode(',', $toMail));
        $resolved = [];
        foreach ($parts as $part) {
            if (strpos($part, '@') !== false) {
                $resolved[] = $part;
                continue;
            }
            if (is_numeric($part)) {
                $email = null;
                $admin = Admin::withoutGlobalScopes()->find($part);
                if ($admin && !empty($admin->email)) {
                    $email = $admin->email;
                }
                if (!$email) {
                    $partner = Partner::find($part);
                    if ($partner && isset($partner->email) && $partner->email !== '') {
                        $email = $partner->email;
                    }
                }
                if (!$email) {
                    $agent = Agent::find($part);
                    if ($agent && !empty($agent->email)) {
                        $email = $agent->email;
                    }
                }
                $resolved[] = $email ?: $part;
            } else {
                $resolved[] = $part;
            }
        }
        return implode(', ', $resolved);
    }
}
