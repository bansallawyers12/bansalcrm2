<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use App\Models\Document;
use App\Models\Email;
use App\Models\ActivitiesLog;
use App\Models\Admin;
use App\Traits\LogsClientActivity;

/**
 * Modern Email Upload Controller
 * 
 * Uses Python microservice for email parsing instead of legacy PEAR libraries.
 * This provides better performance, modern code, and PHP 8.2+ compatibility.
 */
class EmailUploadV2Controller extends Controller
{
    use LogsClientActivity;

    /**
     * Python service configuration
     */
    protected string $pythonServiceUrl;

    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->pythonServiceUrl = env('PYTHON_SERVICE_URL', 'http://127.0.0.1:5001');
    }

    /**
     * Allowed upload extensions from config (e.g. msg, eml).
     *
     * @return list<string>
     */
    protected function allowedEmailUploadExtensions(): array
    {
        $exts = config('crm.email_upload_allowed_extensions', ['msg']);

        return array_values(array_filter(array_map(
            static fn ($ext) => strtolower(ltrim((string) $ext, '.')),
            is_array($exts) ? $exts : ['msg']
        )));
    }

    /**
     * Public URL for an object on the configured S3 disk.
     */
    protected function s3PublicUrl(string $path): string
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('s3');

        return $disk->url($path);
    }

    protected function emailUploadMaxKb(): int
    {
        return max(1, (int) config('crm.email_upload_max_kb', 30720));
    }

    protected function isAllowedEmailUploadExtension(string $filename): bool
    {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        return $ext !== '' && in_array($ext, $this->allowedEmailUploadExtensions(), true);
    }

    /**
     * @return array<string, mixed>
     */
    protected function emailUploadValidationRules(): array
    {
        $maxKb = $this->emailUploadMaxKb();
        $mimes = implode(',', $this->allowedEmailUploadExtensions());

        return [
            'email_files' => 'required',
            'email_files.*' => "file|max:{$maxKb}|mimes:{$mimes}",
            'client_id' => 'required',
            'type' => 'required|in:client,lead,partner',
            'email_category' => 'nullable|in:client,college',
            'label_ids' => 'nullable|array|max:10',
            'label_ids.*' => 'integer|exists:email_labels,id|distinct',
        'force_upload' => 'nullable|boolean',
        'attachment_storage' => 'nullable|string',
    ];
}

    protected function allowedExtensionsLabel(): string
    {
        return implode(', ', array_map(
            static fn ($ext) => '.' . $ext,
            $this->allowedEmailUploadExtensions()
        ));
    }

    /**
     * Find an existing email that matches the uploaded file for this client/partner.
     */
    protected function findExistingEmail(
        int $clientId,
        string $mailType,
        string $recordType,
        ?string $emailCategory,
        array $parsedData,
        string $fileHash
    ): ?Email {
        $query = Email::query()
            ->where('client_id', $clientId)
            ->where('mail_body_type', $mailType)
            ->where('type', $recordType);

        if ($recordType !== 'partner') {
            $query->where('email_category', $emailCategory ?: 'client');
        }

        $byHash = (clone $query)->where('file_hash', $fileHash)->first();
        if ($byHash) {
            return $byHash;
        }

        $messageId = trim((string) ($parsedData['message_id'] ?? ''));
        if ($messageId !== '') {
            $byMessageId = (clone $query)->where('message_id', $messageId)->first();
            if ($byMessageId) {
                return $byMessageId;
            }
        }

        $subject = trim((string) ($parsedData['subject'] ?? ''));
        $sender = trim((string) ($parsedData['sender_email'] ?? ''));
        if ($subject !== '' && $sender !== '') {
            $dupQuery = (clone $query)
                ->where('subject', $subject)
                ->where('from_mail', $sender);

            $sentStorage = $this->sentTimeStorageStringFromParsed($parsedData['sent_date'] ?? null);
            if ($sentStorage) {
                $dupQuery->where('fetch_mail_sent_time', $sentStorage);
            }

            $existing = $dupQuery->first();
            if ($existing) {
                return $existing;
            }
        }

        return null;
    }

    protected function buildDuplicateErrorMessage(Email $existing): string
    {
        $subject = $existing->subject ?: '(No subject)';
        $from = $existing->from_mail ?: 'Unknown sender';
        $sent = $existing->fetch_mail_sent_time ?: null;

        $message = 'This email already exists.';
        $message .= ' Subject: "' . $subject . '" from ' . $from;
        if ($sent) {
            $message .= ' (sent ' . $sent . ')';
        }

        return $message;
    }

    protected function sentTimeStorageStringFromParsed(?string $dateString): ?string
    {
        if (empty($dateString)) {
            return null;
        }

        try {
            if (preg_match('/[+-]\d{2}:\d{2}$|Z$/', $dateString)) {
                $sentDate = new \DateTime($dateString);
            } else {
                $sentDate = new \DateTime($dateString, new \DateTimeZone('UTC'));
            }
            $sentDate->setTimezone(new \DateTimeZone('Australia/Melbourne'));

            return $sentDate->format('d/m/Y h:i a');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @return array{filename: string, error: string, duplicate?: bool, existing?: array<string, mixed>}|null
     */
    protected function formatUploadFailureResult(array $result, string $filename): array
    {
        $entry = [
            'filename' => $filename,
            'error' => $result['error'] ?? 'Unknown error occurred while processing email',
        ];

        if (!empty($result['duplicate'])) {
            $entry['duplicate'] = true;
            $entry['existing'] = $result['existing'] ?? null;
        }

        return $entry;
    }

    /**
     * Preview attachment metadata from an email file before upload (no S3 save).
     */
    public function previewEmailAttachments(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), $this->emailUploadValidationRules());
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $file = $request->file('email_files')[0] ?? null;
            if (!$file) {
                return response()->json([
                    'status' => false,
                    'message' => 'No file uploaded',
                ], 400);
            }

            $parsedData = $this->parseEmailMetadataWithPython($file);
            if (!$parsedData || isset($parsedData['error']) || (isset($parsedData['success']) && !$parsedData['success'])) {
                return response()->json([
                    'status' => false,
                    'message' => $parsedData['error'] ?? 'Failed to parse email',
                    'technical_error' => $parsedData['technical_error'] ?? null,
                ], 400);
            }

            $attachments = [];
            foreach ($parsedData['attachments'] ?? [] as $index => $attachmentData) {
                if (!empty($attachmentData['is_inline'])) {
                    continue;
                }
                $filename = $attachmentData['filename'] ?? ('attachment_' . ($index + 1));
                $attachments[] = [
                    'index' => $index,
                    'filename' => $filename,
                    'display_name' => $attachmentData['display_name'] ?? $filename,
                    'file_size' => $attachmentData['file_size'] ?? $attachmentData['size'] ?? 0,
                    'content_type' => $attachmentData['content_type'] ?? 'application/octet-stream',
                ];
            }

            return response()->json([
                'status' => true,
                'attachments' => $attachments,
                'has_attachments' => count($attachments) > 0,
            ]);
        } catch (\Exception $e) {
            Log::error('Preview email attachments error', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Failed to preview attachments: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Re-parse stored email and return rendered HTML for the reading pane.
     */
    public function getParsedEmailHtml(int|string $id)
    {
        try {
            $email = Email::query()->findOrFail($id);

            if (!empty($email->python_rendering) && is_array($email->python_rendering)) {
                $html = $email->python_rendering['rendered_html']
                    ?? $email->python_rendering['enhanced_html']
                    ?? null;
                if ($html) {
                    return response()->json(['success' => true, 'html' => $html]);
                }
            }

            if (!empty($email->rendered_html)) {
                return response()->json(['success' => true, 'html' => $email->rendered_html]);
            }

            if (!empty($email->message)) {
                return response()->json(['success' => true, 'html' => $email->message]);
            }

            if (empty($email->uploaded_doc_id)) {
                return response()->json(['error' => 'No stored email file for this record'], 404);
            }

            $document = Document::query()->find($email->uploaded_doc_id);
            if (!$document || empty($document->myfile_key)) {
                return response()->json(['error' => 'Original email file not found'], 404);
            }

            $entityType = $email->type ?? 'client';
            $clientUniqueId = '';
            if ($entityType === 'partner') {
                $partnerInfo = \App\Models\Partner::select('id')->where('id', $email->client_id)->first();
                $clientUniqueId = !empty($partnerInfo) ? (string) $partnerInfo->id : '';
            } else {
                $adminInfo = Admin::select('client_id')->where('id', $email->client_id)->first();
                $clientUniqueId = !empty($adminInfo) ? (string) $adminInfo->client_id : '';
            }

            $docType = ($entityType === 'partner') ? 'partner_email_fetch' : ($document->doc_type ?? 'conversion_email_fetch');
            $mailType = $document->mail_type ?? $email->mail_body_type ?? 'inbox';
            $sanitizedClientId = preg_replace('/[^a-zA-Z0-9\-_\.]/', '_', $clientUniqueId);
            $s3Key = $sanitizedClientId . '/' . $docType . '/' . $mailType . '/' . $document->myfile_key;

            if (!Storage::disk('s3')->exists($s3Key) && !empty($document->myfile)) {
                $parsed = parse_url($document->myfile);
                if (!empty($parsed['path'])) {
                    $s3Key = ltrim(rawurldecode($parsed['path']), '/');
                    $bucket = (string) config('filesystems.disks.s3.bucket');
                    if ($bucket !== '' && str_starts_with($s3Key, $bucket . '/')) {
                        $s3Key = substr($s3Key, strlen($bucket) + 1);
                    }
                }
            }

            if (!Storage::disk('s3')->exists($s3Key)) {
                return response()->json(['error' => 'File not found in storage'], 404);
            }

            $fileContents = Storage::disk('s3')->get($s3Key);
            $filename = $document->myfile_key ?: 'email.msg';
            $appTimezone = config('app.timezone', 'Australia/Melbourne');

            $response = Http::timeout(90)
                ->attach('file', $fileContents, $filename)
                ->post($this->pythonServiceUrl . '/email/parse-render-pdf?timezone=' . urlencode($appTimezone), [
                    'timezone' => $appTimezone,
                ]);

            if ($response->successful()) {
                $result = $response->json();
                $rendering = $result['rendering'] ?? [];
                $html = $rendering['rendered_html']
                    ?? $result['rendered_html']
                    ?? $result['html_content']
                    ?? $result['text_content']
                    ?? '<div style="padding:20px;">Could not extract email body.</div>';

                return response()->json(['success' => true, 'html' => $html]);
            }

            return response()->json([
                'success' => false,
                'error' => 'Failed to parse email. Python service returned: ' . $response->status(),
            ], 500);
        } catch (\Exception $e) {
            Log::error('Dynamic email parse error: ' . $e->getMessage(), ['id' => $id]);

            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Upload and process inbox emails using Python microservice
     * 
     * Modern replacement for uploadfetchmail method
     */
    public function uploadInboxEmails(Request $request)
    {
        try {
            // Validate file input
            $validator = Validator::make($request->all(), $this->emailUploadValidationRules());

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Additional validation: Ensure labels are active
            if ($request->has('label_ids') && is_array($request->label_ids)) {
                $activeLabelCount = \App\Models\EmailLabel::query()->whereIn('id', $request->label_ids)
                    ->where('is_active', true)
                    ->count();
                
                if ($activeLabelCount !== count($request->label_ids)) {
                    return response()->json([
                        'status' => false,
                        'message' => 'One or more selected labels are invalid or inactive',
                    ], 422);
                }
            }

            $clientId = $request->client_id;
            $entityType = $request->type;
            
            // Get unique ID based on entity type
            $clientUniqueId = "";
            if ($entityType === 'partner') {
                $partnerInfo = \App\Models\Partner::select('id')->where('id', $clientId)->first();
                $clientUniqueId = !empty($partnerInfo) ? (string)$partnerInfo->id : "";
            } else {
                // For client/lead, use Admin model
                $clientInfo = Admin::select('client_id')->where('id', $clientId)->first();
                $clientUniqueId = !empty($clientInfo) ? $clientInfo->client_id : "";
            }

            if (!$request->hasfile('email_files')) {
                return response()->json([
                    'status' => false,
                    'message' => 'No files uploaded',
                ], 400);
            }

            // Check maximum file limit (10 emails max)
            $emailFiles = $request->file('email_files');
            $fileCount = is_array($emailFiles) ? count($emailFiles) : 0;
            
            if ($fileCount > 10) {
                return response()->json([
                    'status' => false,
                    'message' => 'Maximum 10 email files allowed per upload. Please select 10 or fewer files.',
                    'uploaded' => 0,
                    'failed' => 0,
                    'errors' => []
                ], 422);
            }

            $uploadedCount = 0;
            $failedCount = 0;
            $errors = [];

            foreach ($request->file('email_files') as $file) {
                try {
                    $result = $this->processEmailFile($file, $clientId, $clientUniqueId, 'inbox', $request);
                    
                    if ($result['success']) {
                        $uploadedCount++;
                    } else {
                        $failedCount++;
                        $errors[] = $this->formatUploadFailureResult($result, $file->getClientOriginalName());
                    }
                } catch (\Exception $e) {
                    $failedCount++;
                    $fileName = $file->getClientOriginalName();
                    $errorMsg = $e->getMessage();
                    
                    // Extract user-friendly error if available
                    $userError = $errorMsg;
                    if (is_array($errorMsg) && isset($errorMsg['error'])) {
                        $userError = $errorMsg['error'];
                    }
                    
                    $errors[] = [
                        'filename' => $fileName,
                        'error' => $userError,
                        'file_size' => $file->getSize(),
                        'file_type' => $file->getMimeType()
                    ];
                    Log::error('Email upload error', [
                        'file' => $fileName,
                        'file_size' => $file->getSize(),
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            // Build user-friendly message
            $message = '';
            $status = true;
            
            if ($uploadedCount > 0 && $failedCount == 0) {
                $message = "Successfully uploaded {$uploadedCount} email" . ($uploadedCount > 1 ? 's' : '');
                $status = true;
            } elseif ($uploadedCount > 0 && $failedCount > 0) {
                $message = "Partially successful: {$uploadedCount} email" . ($uploadedCount > 1 ? 's' : '') . " uploaded, {$failedCount} failed";
                $status = true; // Partial success is still considered success
            } elseif ($failedCount > 0) {
                $message = "Upload failed: {$failedCount} email" . ($failedCount > 1 ? 's' : '') . " could not be processed";
                $status = false;
            } else {
                $message = "No emails were processed";
                $status = false;
            }
            
            // Return response with proper status
            return response()->json([
                'status' => $status,
                'message' => $message,
                'uploaded' => $uploadedCount,
                'failed' => $failedCount,
                'errors' => $errors,
                'total_files' => $uploadedCount + $failedCount
            ], $status ? 200 : 400);

        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $allowedLabel = $this->allowedExtensionsLabel();
            
            if (strpos($errorMessage, 'Validation failed') !== false) {
                $errorMessage = "File validation failed. Please upload Outlook email files only ({$allowedLabel}, max {$this->emailUploadMaxKb()}KB each).";
            } elseif (strpos($errorMessage, 'No files uploaded') !== false) {
                $errorMessage = "No files were selected for upload. Please select at least one email file ({$allowedLabel}).";
            }
            
            Log::error('Email upload error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_friendly_error' => $errorMessage
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Upload failed: ' . $errorMessage,
                'technical_error' => $e->getMessage() // Include original for debugging
            ], 500);
        }
    }

    /**
     * Upload and process sent emails using Python microservice
     */
    public function uploadSentEmails(Request $request)
    {
        try {
            // Validate file input
            $validator = Validator::make($request->all(), $this->emailUploadValidationRules());

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Additional validation: Ensure labels are active
            if ($request->has('label_ids') && is_array($request->label_ids)) {
                $activeLabelCount = \App\Models\EmailLabel::query()->whereIn('id', $request->label_ids)
                    ->where('is_active', true)
                    ->count();
                
                if ($activeLabelCount !== count($request->label_ids)) {
                    return response()->json([
                        'status' => false,
                        'message' => 'One or more selected labels are invalid or inactive',
                    ], 422);
                }
            }

            $clientId = $request->client_id;
            $entityType = $request->type;
            
            // Get unique ID based on entity type
            $clientUniqueId = "";
            if ($entityType === 'partner') {
                $partnerInfo = \App\Models\Partner::select('id')->where('id', $clientId)->first();
                $clientUniqueId = !empty($partnerInfo) ? (string)$partnerInfo->id : "";
            } else {
                // For client/lead, use Admin model
                $clientInfo = Admin::select('client_id')->where('id', $clientId)->first();
                $clientUniqueId = !empty($clientInfo) ? $clientInfo->client_id : "";
            }

            if (!$request->hasfile('email_files')) {
                return response()->json([
                    'status' => false,
                    'message' => 'No files uploaded',
                ], 400);
            }

            // Check maximum file limit (10 emails max)
            $emailFiles = $request->file('email_files');
            $fileCount = is_array($emailFiles) ? count($emailFiles) : 0;
            
            if ($fileCount > 10) {
                return response()->json([
                    'status' => false,
                    'message' => 'Maximum 10 email files allowed per upload. Please select 10 or fewer files.',
                    'uploaded' => 0,
                    'failed' => 0,
                    'errors' => []
                ], 422);
            }

            $uploadedCount = 0;
            $failedCount = 0;
            $errors = [];

            foreach ($request->file('email_files') as $file) {
                try {
                    $result = $this->processEmailFile($file, $clientId, $clientUniqueId, 'sent', $request);
                    
                    if ($result['success']) {
                        $uploadedCount++;
                    } else {
                        $failedCount++;
                        $errors[] = $this->formatUploadFailureResult($result, $file->getClientOriginalName());
                    }
                } catch (\Exception $e) {
                    $failedCount++;
                    $fileName = $file->getClientOriginalName();
                    $errorMsg = $e->getMessage();
                    
                    // Extract user-friendly error if available
                    $userError = $errorMsg;
                    if (is_array($errorMsg) && isset($errorMsg['error'])) {
                        $userError = $errorMsg['error'];
                    }
                    
                    $errors[] = [
                        'filename' => $fileName,
                        'error' => $userError,
                        'file_size' => $file->getSize(),
                        'file_type' => $file->getMimeType()
                    ];
                    Log::error('Email upload error', [
                        'file' => $fileName,
                        'file_size' => $file->getSize(),
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            // Build user-friendly message
            $message = '';
            $status = true;
            
            if ($uploadedCount > 0 && $failedCount == 0) {
                $message = "Successfully uploaded {$uploadedCount} email" . ($uploadedCount > 1 ? 's' : '');
                $status = true;
            } elseif ($uploadedCount > 0 && $failedCount > 0) {
                $message = "Partially successful: {$uploadedCount} email" . ($uploadedCount > 1 ? 's' : '') . " uploaded, {$failedCount} failed";
                $status = true;
            } elseif ($failedCount > 0) {
                $message = "Upload failed: {$failedCount} email" . ($failedCount > 1 ? 's' : '') . " could not be processed";
                $status = false;
            } else {
                $message = "No emails were processed";
                $status = false;
            }

            return response()->json([
                'status' => $status,
                'message' => $message,
                'uploaded' => $uploadedCount,
                'failed' => $failedCount,
                'errors' => $errors,
                'total_files' => $uploadedCount + $failedCount
            ], $status ? 200 : 400);

        } catch (\Exception $e) {
            $allowedLabel = $this->allowedExtensionsLabel();
            Log::error('Sent email upload error', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Upload failed: ' . $e->getMessage() . " (Allowed: {$allowedLabel})",
            ], 500);
        }
    }

    /**
     * Process individual email file using Python microservice
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @param int $clientId
     * @param string $clientUniqueId
     * @param string $mailType (inbox|sent)
     * @param Request $request
     * @return array
     */
    protected function processEmailFile($file, $clientId, $clientUniqueId, $mailType, $request)
    {
        try {
            $fileName = $file->getClientOriginalName();
            $fileSize = $file->getSize();

            if ($fileSize <= 0) {
                throw new \Exception('Uploaded file is empty. Save the email from Outlook again and retry.');
            }

            if (!$this->isAllowedEmailUploadExtension($fileName)) {
                throw new \Exception('Invalid file type. Allowed: ' . $this->allowedExtensionsLabel());
            }
            
            // Sanitize filename for S3 path to prevent 403 errors with special characters
            $sanitizedFileName = $this->sanitizeFilename($fileName);
            $uniqueFileName = time() . '-' . $sanitizedFileName;
            
            // Set doc_type based on entity type
            $entityType = $request->type;
            $docType = ($entityType === 'partner') ? 'partner_email_fetch' : 'conversion_email_fetch';

            // 1. Parse email first (before S3 — enables duplicate check without storage upload)
            $parsedData = $this->parseEmailWithPython($file);

            if (!$parsedData || isset($parsedData['error']) || (isset($parsedData['success']) && !$parsedData['success'])) {
                throw new \Exception($parsedData['error'] ?? 'Failed to parse email');
            }

            $fileHash = md5_file($file->getRealPath());
            $emailCategory = $entityType !== 'partner'
                ? $request->input('email_category', 'client')
                : null;

            if (!$request->boolean('force_upload')) {
                $existing = $this->findExistingEmail(
                    (int) $clientId,
                    $mailType,
                    (string) $entityType,
                    $emailCategory,
                    $parsedData,
                    $fileHash
                );

                if ($existing) {
                    return [
                        'success' => false,
                        'duplicate' => true,
                        'error' => $this->buildDuplicateErrorMessage($existing),
                        'existing' => [
                            'id' => $existing->id,
                            'subject' => $existing->subject,
                            'from_mail' => $existing->from_mail,
                            'sent_date' => $existing->fetch_mail_sent_time,
                        ],
                    ];
                }
            }
            
            // 2. Upload file to S3 (use sanitized filename in path)
            $sanitizedClientId = preg_replace('/[^a-zA-Z0-9\-_\.]/', '_', $clientUniqueId);
            $filePath = $sanitizedClientId . '/' . $docType . '/' . $mailType . '/' . $uniqueFileName;
            
            try {
                $fileContents = file_get_contents($file->getPathname());
                if ($fileContents === false) {
                    throw new \Exception('Failed to read email file contents');
                }
                
                $uploadResult = Storage::disk('s3')->put($filePath, $fileContents);
                if (!$uploadResult) {
                    throw new \Exception('Failed to upload file to storage. Please check storage configuration.');
                }
            } catch (\Exception $s3Exception) {
                Log::error('S3 upload failed for email', [
                    'file' => $fileName,
                    's3_path' => $filePath,
                    'error' => $s3Exception->getMessage()
                ]);
                throw new \Exception('File storage error: ' . $s3Exception->getMessage());
            }
            
            try {
                $fileUrl = $this->s3PublicUrl($filePath);
                if (empty($fileUrl)) {
                    throw new \Exception('Failed to generate file URL');
                }
            } catch (\Exception $urlException) {
                Log::error('S3 URL generation failed', [
                    'file' => $fileName,
                    's3_path' => $filePath,
                    'error' => $urlException->getMessage()
                ]);
                throw new \Exception('File URL generation error: ' . $urlException->getMessage());
            }

            // 3. Save document record
            $document = new Document();
            $document->file_name = pathinfo($fileName, PATHINFO_FILENAME);
            $document->filetype = pathinfo($fileName, PATHINFO_EXTENSION);
            $document->user_id = Auth::user()->id;
            $document->myfile = $fileUrl;
            $document->myfile_key = $uniqueFileName;
            $document->client_id = $clientId;
            $document->type = $request->type;
            $document->mail_type = $mailType;
            $document->file_size = $fileSize;
            $document->doc_type = $docType;
            
            try {
                $document->save();
            } catch (QueryException $e) {
                Log::error('Failed to save Document record', [
                    'file' => $fileName,
                    'document_data' => $document->toArray(),
                    'error' => $e->getMessage(),
                    'error_info' => $e->errorInfo ?? []
                ]);
                throw new \Exception('Failed to save document record: ' . ($e->errorInfo[2] ?? $e->getMessage()));
            }

            $pdfDocumentId = $this->saveEmailPdfDocument(
                $parsedData,
                $fileName,
                $sanitizedClientId,
                $docType,
                $mailType,
                $uniqueFileName,
                $clientId,
                $request
            );

            // 4. Save to Email (emails table)
            $mailReport = new Email();
            $mailReport->user_id = Auth::user()->id;
            $mailReport->from_mail = $parsedData['sender_email'] ?? '';
            $mailReport->to_mail = isset($parsedData['recipients']) && is_array($parsedData['recipients']) 
                ? implode(',', $parsedData['recipients']) 
                : '';
            $mailReport->subject = $parsedData['subject'] ?? '';

            $rendering = $parsedData['rendering'] ?? null;
            $htmlBody = is_array($rendering)
                ? ($rendering['rendered_html'] ?? $rendering['enhanced_html'] ?? null)
                : ($parsedData['rendered_html'] ?? null);
            $mailReport->message = $htmlBody
                ?? $parsedData['html_content']
                ?? $parsedData['text_content']
                ?? '';

            if (is_array($rendering)) {
                $mailReport->python_rendering = $rendering;
                $mailReport->rendered_html = $rendering['rendered_html'] ?? null;
                $mailReport->enhanced_html = $rendering['enhanced_html'] ?? null;
                $mailReport->text_preview = $rendering['text_preview'] ?? ($parsedData['text_preview'] ?? null);
            } elseif (!empty($parsedData['text_preview'])) {
                $mailReport->text_preview = $parsedData['text_preview'];
            }

            $mailReport->mail_type = 1;
            $mailReport->type = $request->type; // Set type to 'client' or 'lead' as required by filter
            if ($request->type !== 'partner') {
                $mailReport->email_category = $request->input('email_category', 'client');
            }
            $mailReport->client_id = $clientId;
            $mailReport->conversion_type = $docType;
            $mailReport->mail_body_type = $mailType;
            $mailReport->uploaded_doc_id = $document->id;
            $mailReport->pdf_doc_id = $pdfDocumentId;
            
            // Format sent time from Python response
            if (!empty($parsedData['sent_date'])) {
                try {
                    // Parse the ISO date string from Python
                    // If timezone is not specified in the string, treat it as UTC
                    $dateString = $parsedData['sent_date'];
                    
                    // Check if the date string has timezone info
                    // ISO format with timezone: "2025-11-17T18:19:00+00:00" or "2025-11-17T18:19:00Z"
                    // ISO format without timezone: "2025-11-17T18:19:00"
                    if (preg_match('/[+-]\d{2}:\d{2}$|Z$/', $dateString)) {
                        // Has timezone info, parse as-is
                        $sentDate = new \DateTime($dateString);
                    } else {
                        // No timezone info, assume UTC (as Python now sends UTC for naive datetimes)
                        $sentDate = new \DateTime($dateString, new \DateTimeZone('UTC'));
                    }
                    
                    // Convert to Australia/Melbourne timezone for display
                    $sentDate->setTimezone(new \DateTimeZone('Australia/Melbourne'));
                    $mailReport->fetch_mail_sent_time = $sentDate->format('d/m/Y h:i a');
                } catch (\Exception $e) {
                    $mailReport->fetch_mail_sent_time = $parsedData['sent_date'];
                }
            }
            
            // NEW: Add Python AI analysis
            $analysisData = $this->analyzeEmailWithPython($parsedData);
            if ($analysisData && isset($analysisData['success']) && $analysisData['success']) {
                // Ensure JSON fields are properly formatted arrays (not objects or strings)
                $mailReport->python_analysis = is_array($analysisData) ? $analysisData : null;
                $mailReport->category = $analysisData['category'] ?? 'Uncategorized';
                $mailReport->priority = $analysisData['priority'] ?? 'low';
                $mailReport->sentiment = $analysisData['sentiment'] ?? 'neutral';
                $mailReport->language = $analysisData['language'] ?? null;
                // Ensure these are arrays or null for JSON columns
                $mailReport->security_issues = isset($analysisData['security_issues']) 
                    ? (is_array($analysisData['security_issues']) ? $analysisData['security_issues'] : null)
                    : null;
                $mailReport->thread_info = isset($analysisData['thread_info'])
                    ? (is_array($analysisData['thread_info']) ? $analysisData['thread_info'] : null)
                    : null;
                $mailReport->processed_at = now();
            }
            
            // NEW: Add metadata
            $mailReport->message_id = $parsedData['message_id'] ?? null;
            $mailReport->thread_id = $parsedData['thread_id'] ?? null;
            
            // Handle received_date with timezone awareness
            if (!empty($parsedData['received_date'])) {
                try {
                    $dateString = $parsedData['received_date'];
                    if (preg_match('/[+-]\d{2}:\d{2}$|Z$/', $dateString)) {
                        $receivedDate = new \DateTime($dateString);
                    } else {
                        $receivedDate = new \DateTime($dateString, new \DateTimeZone('UTC'));
                    }
                    // Convert to Australia/Melbourne timezone
                    $receivedDate->setTimezone(new \DateTimeZone('Australia/Melbourne'));
                    $mailReport->received_date = $receivedDate;
                } catch (\Exception $e) {
                    $mailReport->received_date = now();
                }
            } else {
                $mailReport->received_date = now();
            }
            
            $mailReport->file_hash = $fileHash;
            
            try {
                $mailReport->save();
            } catch (QueryException $e) {
                Log::error('Failed to save Email record', [
                    'file' => $fileName,
                    'document_id' => $document->id,
                    'mail_report_data' => $mailReport->toArray(),
                    'error' => $e->getMessage(),
                    'error_info' => $e->errorInfo ?? [],
                    'sql' => $e->getSql() ?? 'N/A'
                ]);
                throw new \Exception('Failed to save email record: ' . ($e->errorInfo[2] ?? $e->getMessage()));
            }

            // NEW: Save attachments
            $attachmentStorageMap = $this->parseAttachmentStorageMap($request);
            if (isset($parsedData['attachments']) && is_array($parsedData['attachments'])) {
                Log::info('Processing attachments', [
                    'count' => count($parsedData['attachments']),
                    'mail_report_id' => $mailReport->id
                ]);
                
                foreach ($parsedData['attachments'] as $attachmentData) {
                    try {
                        $originalName = $attachmentData['filename'] ?? '';
                        $storageConfig = $attachmentStorageMap[$originalName] ?? null;
                        $this->saveAttachment(
                            $mailReport->id,
                            $attachmentData,
                            $clientUniqueId,
                            $storageConfig,
                            $request,
                            (int) $clientId
                        );
                    } catch (\Exception $e) {
                        Log::error('Error in saveAttachment loop', [
                            'error' => $e->getMessage(),
                            'attachment' => $attachmentData['filename'] ?? 'unknown'
                        ]);
                        // Continue processing other attachments
                    }
                }
            } else {
                Log::info('No attachments found in parsed data', [
                    'has_attachments_key' => isset($parsedData['attachments']),
                    'mail_report_id' => $mailReport->id
                ]);
            }

            // NEW: Assign manually selected labels first
            if ($request->has('label_ids') && is_array($request->label_ids)) {
                $this->assignLabels($mailReport, $request->label_ids, 'manual');
            }

            // Auto-assign labels (Sent/Inbox)
            $this->autoAssignLabels($mailReport, $mailType);

            // 5. Create activity log
            $entityType = $request->type;
            if ($entityType == 'client') {
                // Get matter reference from latest active matter (if ClientMatter model exists)
                $matterReference = '';
                if (class_exists('App\Models\ClientMatter')) {
                    try {
                        $latestMatter = \App\Models\ClientMatter::where('client_id', $clientId)
                            ->where('matter_status', 1)
                            ->orderBy('id', 'desc')
                            ->first();
                        if ($latestMatter && isset($latestMatter->client_unique_matter_no)) {
                            $matterReference = $latestMatter->client_unique_matter_no;
                        }
                    } catch (\Exception $e) {
                        // Skip if model doesn't exist
                    }
                }
                
                // Format subject with matter reference
                $emailSubject = $parsedData['subject'] ?? 'Email';
                $subject = !empty($matterReference)
                    ? "uploaded Email: {$emailSubject} - {$matterReference}"
                    : "uploaded Email: {$emailSubject}";
                
                // Truncate long subjects
                if (strlen($subject) > 100) {
                    $subject = substr($subject, 0, 97) . '...';
                }
                
                $from = $parsedData['from'] ?? 'Unknown';
                $description = "<p>From: {$from}</p>";
                
                $this->logClientActivity(
                    $clientId,
                    $subject,
                    $description,
                    'email'
                );
            } elseif ($entityType == 'partner') {
                // Log partner activity (similar structure but for partners)
                $emailSubject = $parsedData['subject'] ?? 'Email';
                $subject = "uploaded Email: {$emailSubject}";
                
                // Truncate long subjects
                if (strlen($subject) > 100) {
                    $subject = substr($subject, 0, 97) . '...';
                }
                
                $from = $parsedData['from'] ?? 'Unknown';
                $description = "<p>From: {$from}</p>";
                
                // Use ActivitiesLog directly for partners (if it supports partner_id)
                try {
                    ActivitiesLog::create([
                        'client_id' => $clientId, // Partners may use client_id field
                        'created_by' => Auth::user()->id ?? Auth::id(),
                        'subject' => $subject,
                        'description' => $description,
                        'use_for' => null, // Integer field for user/category assignment
                        'task_status' => 0,
                        'pin' => 0,
                    ]);
                } catch (\Exception $e) {
                    Log::warning('Failed to log partner email activity', ['error' => $e->getMessage()]);
                }
            }

            return [
                'success' => true,
                'document_id' => $document->id,
                'mail_report_id' => $mailReport->id
            ];

        } catch (\Illuminate\Database\QueryException $e) {
            $errorMessage = $e->getMessage();
            $fileName = $file->getClientOriginalName();
            
            // Extract more specific database error information
            $errorCode = $e->getCode();
            $errorInfo = $e->errorInfo ?? [];
            
            // PostgreSQL specific errors
            if (isset($errorInfo[0]) && $errorInfo[0] === '23502') {
                $errorMessage = "Database constraint error: Required field is missing. Please check the email data.";
            } elseif (isset($errorInfo[0]) && $errorInfo[0] === '23505') {
                $errorMessage = "Duplicate entry: This email may already exist in the database.";
            } elseif (isset($errorInfo[0]) && $errorInfo[0] === '22P02' || strpos($errorMessage, 'invalid input syntax') !== false) {
                $errorMessage = "Data format error: Invalid data format for one or more fields. The email may contain invalid characters or formatting.";
            } elseif (strpos($errorMessage, 'json') !== false || strpos($errorMessage, 'JSON') !== false) {
                $errorMessage = "JSON data error: Unable to save email metadata. Please try again or contact support.";
            } else {
                $errorMessage = "Database error: " . ($errorInfo[2] ?? $errorMessage);
            }
            
            Log::error('Process email file database error', [
                'file' => $fileName,
                'error' => $e->getMessage(),
                'error_code' => $errorCode,
                'error_info' => $errorInfo,
                'sql' => $e->getSql() ?? 'N/A',
                'bindings' => $e->getBindings() ?? [],
                'trace' => $e->getTraceAsString(),
                'user_friendly_error' => $errorMessage
            ]);

            return [
                'success' => false,
                'error' => $errorMessage,
                'technical_error' => $e->getMessage() // Include original for debugging
            ];
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $fileName = $file->getClientOriginalName();
            
            // Make error messages more user-friendly
            if (strpos($errorMessage, 'Failed to connect') !== false || strpos($errorMessage, 'Connection refused') !== false) {
                $errorMessage = "Cannot connect to email processing service. Please ensure the Python service is running at {$this->pythonServiceUrl}";
            } elseif (stripos($errorMessage, 'Access is denied') !== false || stripos($errorMessage, 'WinError 5') !== false) {
                $errorMessage = "Email processing service could not access temporary files. The Python service has been updated to use the system temp folder; please restart the Python service and try again.";
            } elseif (strpos($errorMessage, 'Failed to parse email') !== false || strpos($errorMessage, 'Python service returned') !== false) {
                $errorMessage = "Failed to parse email file. The file may be corrupted or in an unsupported format.";
            } elseif (strpos($errorMessage, 'S3') !== false || strpos($errorMessage, 'AWS') !== false || strpos($errorMessage, 'storage') !== false) {
                $errorMessage = "File storage error. Please check S3 configuration or try again.";
            } elseif (strpos($errorMessage, 'database') !== false || strpos($errorMessage, 'SQL') !== false) {
                $errorMessage = "Database error. Please try again or contact support if the issue persists.";
            }
            
            Log::error('Process email file error', [
                'file' => $fileName,
                'error' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'trace' => $e->getTraceAsString(),
                'user_friendly_error' => $errorMessage
            ]);

            return [
                'success' => false,
                'error' => $errorMessage,
                'technical_error' => $e->getMessage() // Include original for debugging
            ];
        }
    }

    /**
     * Parse email metadata only (no PDF render) — used for attachment preview before upload.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return array|null
     */
    protected function parseEmailMetadataWithPython($file)
    {
        return $this->callPythonEmailEndpoint($file, '/email/parse', 30);
    }

    /**
     * Parse email file using Python microservice (includes PDF generation, soft-fail).
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return array|null
     */
    protected function parseEmailWithPython($file)
    {
        return $this->callPythonEmailEndpoint($file, '/email/parse-render-pdf', 180);
    }

    /**
     * @return array{success: false, error: string, technical_error?: string}|array<string, mixed>
     */
    protected function callPythonEmailEndpoint(\Illuminate\Http\UploadedFile $file, string $path, int $timeout)
    {
        try {
            $originalFileName = $file->getClientOriginalName();
            $sanitizedFileName = $this->sanitizeFilename($originalFileName);
            $appTimezone = config('app.timezone', 'Australia/Melbourne');

            $response = Http::timeout($timeout)
                ->attach('file', file_get_contents($file->getPathname()), $sanitizedFileName)
                ->post($this->pythonServiceUrl . $path . '?timezone=' . urlencode($appTimezone), [
                    'timezone' => $appTimezone,
                ]);

            if ($response->successful()) {
                try {
                    $result = $response->json();
                } catch (\Exception $jsonException) {
                    Log::error('Failed to parse Python service response as JSON', [
                        'path' => $path,
                        'status' => $response->status(),
                        'content_type' => $response->header('Content-Type'),
                        'body_preview' => substr($response->body(), 0, 500),
                        'error' => $jsonException->getMessage()
                    ]);
                    return [
                        'success' => false,
                        'error' => 'Invalid response from email processing service. The service may be experiencing issues.'
                    ];
                }

                if (isset($result['error']) || (isset($result['success']) && !$result['success'])) {
                    return [
                        'success' => false,
                        'error' => $result['error'] ?? 'Email parsing failed'
                    ];
                }
                return $result;
            }

            $body = $response->body();
            Log::error('Python service error', [
                'path' => $path,
                'status' => $response->status(),
                'body' => $body
            ]);

            $errorMsg = 'Python service returned status: ' . $response->status();
            $decoded = json_decode($body, true);
            if (is_array($decoded) && isset($decoded['error'])) {
                $errorMsg = is_string($decoded['error']) ? $decoded['error'] : $errorMsg;
            } elseif (is_array($decoded) && isset($decoded['detail'])) {
                $detail = $decoded['detail'];
                $errorMsg = is_string($detail) ? $detail : $errorMsg;
            }

            return [
                'success' => false,
                'error' => $errorMsg
            ];

        } catch (\Exception $e) {
            Log::error('Python service connection error', [
                'path' => $path,
                'error' => $e->getMessage(),
                'url' => $this->pythonServiceUrl
            ]);

            return [
                'success' => false,
                'error' => 'Failed to connect to Python service: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Save generated PDF to S3 and create a documents row (soft-fail returns null).
     *
     * @return int|null Document id for the PDF, or null if PDF was not generated/saved
     */
    protected function saveEmailPdfDocument(
        array $parsedData,
        string $fileName,
        string $sanitizedClientId,
        string $docType,
        string $mailType,
        string $uniqueFileName,
        int $clientId,
        Request $request
    ): ?int {
        if (empty($parsedData['pdf_base64'])) {
            if (!empty($parsedData['pdf_error'])) {
                Log::warning('Email PDF not generated', [
                    'file' => $fileName,
                    'error' => $parsedData['pdf_error'],
                ]);
            }
            return null;
        }

        try {
            $pdfBytes = base64_decode($parsedData['pdf_base64'], true);
            if ($pdfBytes === false || strlen($pdfBytes) === 0) {
                Log::warning('Failed to decode email PDF from Python service', ['file' => $fileName]);
                return null;
            }

            $pdfUniqueFileName = preg_replace('/\.(msg|eml)$/i', '.pdf', $uniqueFileName);
            if ($pdfUniqueFileName === $uniqueFileName) {
                $pdfUniqueFileName = pathinfo($uniqueFileName, PATHINFO_FILENAME) . '.pdf';
            }

            $pdfFilePath = $sanitizedClientId . '/' . $docType . '/' . $mailType . '/' . $pdfUniqueFileName;

            $uploadResult = Storage::disk('s3')->put($pdfFilePath, $pdfBytes);
            if (!$uploadResult) {
                Log::warning('Failed to upload email PDF to S3', [
                    'file' => $fileName,
                    's3_path' => $pdfFilePath,
                ]);
                return null;
            }

            $pdfFileUrl = $this->s3PublicUrl($pdfFilePath);

            $pdfDocument = new Document();
            $pdfDocument->file_name = pathinfo($fileName, PATHINFO_FILENAME);
            $pdfDocument->filetype = 'pdf';
            $pdfDocument->user_id = Auth::user()->id;
            $pdfDocument->myfile = $pdfFileUrl;
            $pdfDocument->myfile_key = $pdfUniqueFileName;
            $pdfDocument->client_id = $clientId;
            $pdfDocument->type = $request->type;
            $pdfDocument->mail_type = $mailType;
            $pdfDocument->file_size = strlen($pdfBytes);
            $pdfDocument->doc_type = $docType;
            $pdfDocument->save();

            return (int) $pdfDocument->id;
        } catch (\Exception $e) {
            Log::warning('Email PDF save failed (upload continues)', [
                'file' => $fileName,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Parse attachment storage preferences sent from the upload UI.
     *
     * @return array<string, array<string, mixed>>
     */
    protected function parseAttachmentStorageMap(Request $request): array
    {
        if (!$request->filled('attachment_storage')) {
            return [];
        }

        $decoded = json_decode((string) $request->input('attachment_storage'), true);
        if (!is_array($decoded)) {
            return [];
        }

        $map = [];
        foreach ($decoded as $item) {
            if (!is_array($item)) {
                continue;
            }
            $key = $item['original_filename'] ?? $item['filename'] ?? null;
            if ($key) {
                $map[$key] = $item;
            }
        }

        return $map;
    }

    protected function sanitizeAttachmentDisplayName(string $name): string
    {
        $name = trim($name);
        $name = preg_replace('/[^a-zA-Z0-9_\-\.\s\$\(\),&+]/', '_', $name);
        $name = preg_replace('/_+/', '_', trim((string) $name, '_'));

        return $name !== '' ? $name : 'attachment';
    }

    /**
     * Store an email attachment copy in the client Documents tab (category folder).
     *
     * @return array{file_path: string, s3_key: string, file_size: int, display_name: string}|null
     */
    protected function saveEmailAttachmentAsDocument(
        array $attachmentData,
        array $storageConfig,
        string $clientUniqueId,
        int $clientId,
        string $recordType,
        string $decodedData
    ): ?array {
        $storageType = $storageConfig['storage_type'] ?? '';
        if ($storageType !== 'documents') {
            return null;
        }

        $categoryId = (int) ($storageConfig['category_id'] ?? 0);
        if ($categoryId <= 0) {
            return null;
        }

        $originalFilename = $attachmentData['filename'] ?? 'attachment';
        $extension = pathinfo($originalFilename, PATHINFO_EXTENSION);
        $customStem = $this->sanitizeAttachmentDisplayName(
            (string) ($storageConfig['file_name'] ?? pathinfo($originalFilename, PATHINFO_FILENAME))
        );
        $displayName = $extension ? ($customStem . '.' . $extension) : $customStem;

        $sanitizedClientId = preg_replace('/[^a-zA-Z0-9\-_\.]/', '_', $clientUniqueId);
        $uniqueFileName = time() . '_' . uniqid() . '_' . $this->sanitizeFilename($displayName);
        $filePath = $sanitizedClientId . '/documents/' . $uniqueFileName;

        $uploadSuccess = Storage::disk('s3')->put($filePath, $decodedData);
        if (!$uploadSuccess) {
            throw new \Exception('Failed to upload attachment to document storage.');
        }

        $fileUrl = $this->s3PublicUrl($filePath);
        $fileSize = strlen($decodedData);

        $document = new Document();
        $document->file_name = $customStem;
        $document->filetype = $extension ?: pathinfo($displayName, PATHINFO_EXTENSION);
        $document->user_id = Auth::user()->id;
        $document->myfile = $fileUrl;
        $document->myfile_key = $uniqueFileName;
        $document->client_id = $clientId;
        $document->type = $recordType;
        $document->file_size = $fileSize;
        $document->doc_type = 'documents';
        $document->category_id = $categoryId;
        $document->checklist = $customStem;
        $document->save();

        return [
            'file_path' => $fileUrl,
            's3_key' => $filePath,
            'file_size' => $fileSize,
            'display_name' => $displayName,
        ];
    }

    /**
     * Check if Python service is available
     * 
     * @return array
     */
    public function checkPythonService()
    {
        try {
            $response = Http::timeout(5)->get($this->pythonServiceUrl . '/health');

            return [
                'status' => $response->successful(),
                'url' => $this->pythonServiceUrl,
                'response' => $response->successful() ? $response->json() : null
            ];

        } catch (\Exception $e) {
            return [
                'status' => false,
                'url' => $this->pythonServiceUrl,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Analyze email content with Python AI service
     * 
     * @param array $parsedData
     * @return array|null
     */
    protected function analyzeEmailWithPython($parsedData)
    {
        try {
            $response = Http::timeout(30)->post($this->pythonServiceUrl . '/email/analyze', [
                'subject' => $parsedData['subject'] ?? '',
                'text_content' => $parsedData['text_content'] ?? '',
                'html_content' => $parsedData['html_content'] ?? '',
                'sender_email' => $parsedData['sender_email'] ?? '',
                'recipients' => $parsedData['recipients'] ?? [],
            ]);

            if ($response->successful()) {
                return $response->json();
            }
            
            Log::warning('Python analyzer service unavailable', ['status' => $response->status()]);
            return null;
        } catch (\Exception $e) {
            Log::warning('Python analyzer service error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Save attachment to database and S3
     * 
     * @param int $mailReportId
     * @param array $attachmentData
     * @param string $clientUniqueId
     */
    protected function saveAttachment(
        $mailReportId,
        $attachmentData,
        $clientUniqueId,
        $storageConfig = null,
        $request = null,
        $clientId = null
    ) {
        $s3Path = null;
        $s3Key = null;
        $fileSize = $attachmentData['file_size'] ?? $attachmentData['size'] ?? 0;
        $displayName = $attachmentData['display_name'] ?? ($attachmentData['filename'] ?? 'unknown');
        
        try {
            $attachmentContent = $attachmentData['content'] ?? $attachmentData['data'] ?? null;
            
            Log::info('Processing attachment data', [
                'filename' => $attachmentData['filename'] ?? 'unknown',
                'has_content' => !empty($attachmentContent),
                'content_length' => !empty($attachmentContent) ? strlen($attachmentContent) : 0,
                'expected_size' => $fileSize
            ]);

            $decodedData = null;
            if (!empty($attachmentContent)) {
                $decodedData = base64_decode($attachmentContent, true);
                
                if ($decodedData === false) {
                    Log::warning('Failed to decode base64 attachment data', [
                        'filename' => $attachmentData['filename'] ?? 'unknown',
                        'content_length' => strlen($attachmentContent)
                    ]);
                } else {
                    $expectedSize = $fileSize;
                    $actualSize = strlen($decodedData);
                    
                    if ($expectedSize > 0) {
                        $sizeDifference = abs($actualSize - $expectedSize);
                        if ($sizeDifference > 3) {
                            Log::warning('Attachment size mismatch', [
                                'filename' => $attachmentData['filename'] ?? 'unknown',
                                'expected' => $expectedSize,
                                'actual' => $actualSize,
                                'difference' => $sizeDifference
                            ]);
                        }
                    }
                    
                    if ($actualSize === 0) {
                        Log::warning('Decoded attachment data is empty', [
                            'filename' => $attachmentData['filename'] ?? 'unknown'
                        ]);
                        $decodedData = null;
                    }
                }
            } else {
                Log::info('Attachment has no content data, creating record without file', [
                    'filename' => $attachmentData['filename'] ?? 'unknown'
                ]);
            }

            $storageType = is_array($storageConfig) ? ($storageConfig['storage_type'] ?? 'email') : 'email';
            if ($decodedData !== null && $storageType === 'documents' && $request && $clientId) {
                $docResult = $this->saveEmailAttachmentAsDocument(
                    $attachmentData,
                    $storageConfig,
                    $clientUniqueId,
                    (int) $clientId,
                    $request->type ?? 'client',
                    $decodedData
                );
                if ($docResult) {
                    $s3Path = $docResult['file_path'];
                    $s3Key = $docResult['s3_key'];
                    $fileSize = $docResult['file_size'];
                    $displayName = $docResult['display_name'];
                }
            } elseif ($decodedData !== null) {
                $attachmentFileName = $attachmentData['filename'] ?? 'attachment';
                if (is_array($storageConfig) && !empty($storageConfig['file_name'])) {
                    $extension = pathinfo($attachmentFileName, PATHINFO_EXTENSION);
                    $customStem = $this->sanitizeAttachmentDisplayName((string) $storageConfig['file_name']);
                    $displayName = $extension ? ($customStem . '.' . $extension) : $customStem;
                    $attachmentFileName = $displayName;
                }

                $sanitizedAttachmentFileName = $this->sanitizeFilename($attachmentFileName);
                $s3Key = $clientUniqueId . '/attachments/' . time() . '_' . uniqid() . '_' . $sanitizedAttachmentFileName;
                
                try {
                    $uploadSuccess = Storage::disk('s3')->put($s3Key, $decodedData);
                    
                    if (!$uploadSuccess) {
                        throw new \Exception('S3 upload returned false');
                    }
                    
                    if (!Storage::disk('s3')->exists($s3Key)) {
                        throw new \Exception('File not found in S3 after upload');
                    }
                    
                    $s3Path = $this->s3PublicUrl($s3Key);
                    $fileSize = strlen($decodedData);
                    
                    Log::info('Attachment saved successfully to S3', [
                        'filename' => $attachmentData['filename'] ?? 'unknown',
                        'size' => $fileSize,
                        's3_key' => $s3Key,
                        's3_path' => $s3Path
                    ]);
                } catch (\Exception $s3Exception) {
                    Log::error('S3 upload failed for attachment', [
                        'filename' => $attachmentData['filename'] ?? 'unknown',
                        's3_key' => $s3Key,
                        'error' => $s3Exception->getMessage(),
                        'trace' => $s3Exception->getTraceAsString()
                    ]);
                    $s3Key = null;
                    $s3Path = null;
                }
            }

            \App\Models\MailReportAttachment::create([
                'mail_report_id' => $mailReportId,
                'filename' => $displayName,
                'display_name' => $displayName,
                'content_type' => $attachmentData['content_type'] ?? 'application/octet-stream',
                'file_path' => $s3Path,
                's3_key' => $s3Key,
                'file_size' => $fileSize,
                'content_id' => $attachmentData['content_id'] ?? null,
                'is_inline' => $attachmentData['is_inline'] ?? false,
                'extension' => pathinfo($displayName, PATHINFO_EXTENSION),
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to save attachment', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'attachment' => $attachmentData['filename'] ?? 'unknown'
            ]);
        }
    }

    /**
     * Assign multiple labels to a mail report (prevents duplicates)
     * 
     * @param \App\Models\Email $mailReport
     * @param array $labelIds
     * @param string $source ('manual'|'auto')
     * @return int Number of labels assigned
     */
    protected function assignLabels($mailReport, $labelIds, $source = 'manual')
    {
        if (empty($labelIds) || !is_array($labelIds)) {
            return 0;
        }

        try {
            // Get currently attached label IDs
            $existingLabelIds = $mailReport->labels()->pluck('email_labels.id')->toArray();
            
            // Filter out already attached labels
            $newLabelIds = array_diff($labelIds, $existingLabelIds);
            
            if (empty($newLabelIds)) {
                return 0;
            }

            // Attach new labels
            $mailReport->labels()->attach($newLabelIds);
            
            Log::info('Labels assigned to email', [
                'mail_report_id' => $mailReport->id,
                'label_ids' => $newLabelIds,
                'source' => $source,
                'count' => count($newLabelIds)
            ]);
            
            return count($newLabelIds);
        } catch (\Exception $e) {
            Log::warning('Failed to assign labels', [
                'error' => $e->getMessage(),
                'mail_report_id' => $mailReport->id
            ]);
            return 0;
        }
    }

    /**
     * Auto-assign labels based on sender domain
     * 
     * @param \App\Models\Email $mailReport
     * @param string $mailType
     */
    protected function autoAssignLabels($mailReport, $mailType)
    {
        try {
            // Company domains that indicate emails WE sent
            $companyDomains = [
                '@bansaleducation.com.au',
                '@bansalimmigration.com.au',
            ];
            
            // Check if email is from our company domains
            $isFromCompany = false;
            $senderEmail = strtolower($mailReport->from_mail);
            
            foreach ($companyDomains as $domain) {
                if (str_contains($senderEmail, $domain)) {
                    $isFromCompany = true;
                    break;
                }
            }
            
            // Assign "Sent" label if from company domain, otherwise "Inbox" label
            $labelName = $isFromCompany ? 'Sent' : 'Inbox';
            
            $label = \App\Models\EmailLabel::query()->where('name', $labelName)
                ->where('type', 'system')
                ->first();
            
            if ($label) {
                $mailReport->labels()->attach($label->id);
                
                Log::info('Auto-assigned label', [
                    'email_id' => $mailReport->id,
                    'sender' => $mailReport->from_mail,
                    'label' => $labelName,
                    'is_from_company' => $isFromCompany
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to auto-assign label', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Sanitize filename for use in S3 file paths
     * Prevents 403 errors caused by special characters in filenames
     * 
     * @param string $filename Original filename
     * @return string Sanitized filename safe for S3 paths
     */
    protected function sanitizeFilename(string $filename): string
    {
        // Get file extension first (before sanitization)
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $nameWithoutExt = pathinfo($filename, PATHINFO_FILENAME);
        
        // Replace special characters with underscores, but keep alphanumeric, hyphens, underscores, and dots
        $sanitizedName = preg_replace('/[^a-zA-Z0-9\-_\.]/', '_', $nameWithoutExt);
        
        // Remove multiple consecutive underscores
        $sanitizedName = preg_replace('/_+/', '_', $sanitizedName);
        
        // Trim underscores from start and end
        $sanitizedName = trim($sanitizedName, '_');
        
        // Ensure filename is not empty
        if (empty($sanitizedName)) {
            $sanitizedName = 'email_' . time();
        }
        
        // Reconstruct filename with extension
        $sanitizedFilename = !empty($extension) ? $sanitizedName . '.' . $extension : $sanitizedName;
        
        // Limit total filename length (including extension) to 255 characters
        if (strlen($sanitizedFilename) > 255) {
            $maxNameLength = 255 - strlen($extension) - 1; // -1 for the dot
            if ($maxNameLength > 0) {
                $sanitizedName = substr($sanitizedName, 0, $maxNameLength);
                $sanitizedFilename = !empty($extension) ? $sanitizedName . '.' . $extension : $sanitizedName;
            } else {
                // If extension itself is too long, just use timestamp
                $sanitizedFilename = 'email_' . time() . (!empty($extension) ? '.' . $extension : '');
            }
        }
        
        return $sanitizedFilename;
    }
}

