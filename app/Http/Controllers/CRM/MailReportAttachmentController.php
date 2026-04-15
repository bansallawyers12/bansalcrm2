<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\MailReportAttachment;
use App\Models\Email;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class MailReportAttachmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Download individual attachment (S3 key, S3 URL in file_path, or local path).
     */
    public function download($id)
    {
        try {
            $attachment = MailReportAttachment::findOrFail($id);

            $binary = $this->resolveAttachmentBinary($attachment);
            if ($binary === null) {
                Log::error('Attachment download failed: Could not resolve file', [
                    'id' => $id,
                    'filename' => $attachment->filename,
                    'file_path' => $attachment->file_path,
                    's3_key' => $attachment->s3_key,
                    'mail_report_id' => $attachment->mail_report_id,
                ]);
                abort(404, 'Attachment file not found');
            }

            $displayName = $this->downloadDisplayFilenameForAttachment($attachment);

            return Response::make($binary, 200, [
                'Content-Type' => $attachment->content_type ?: 'application/octet-stream',
                'Content-Disposition' => 'attachment; filename="' . $this->escapeContentDispositionFilename($displayName) . '"',
                'Content-Length' => strlen($binary),
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'Attachment not found');
        } catch (\Exception $e) {
            Log::error('Attachment download failed', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            abort(404, 'Attachment file not found');
        }
    }

    /**
     * Download from emails.attachments JSON when no MailReportAttachment row exists (id is null in API).
     */
    public function downloadLegacy(int $mailReportId, int $index)
    {
        $email = Email::findOrFail($mailReportId);
        $raw = $email->getAttributes()['attachments'] ?? null;
        if (empty($raw)) {
            abort(404, 'No legacy attachments');
        }
        $items = is_string($raw) ? json_decode($raw, true) : $raw;
        if (!is_array($items) || !isset($items[$index])) {
            abort(404, 'Attachment index not found');
        }
        $item = $items[$index];
        $fileUrl = $item['file_url'] ?? '';
        $displayName = $this->downloadDisplayFilenameForLegacyItem($item, $fileUrl);

        $binary = $this->resolveBinaryFromLegacyFileUrl($fileUrl);
        if ($binary === null) {
            Log::warning('Legacy attachment download failed', [
                'mail_report_id' => $mailReportId,
                'index' => $index,
                'file_url' => $fileUrl,
            ]);
            abort(404, 'Attachment file not found');
        }

        $ext = strtolower(pathinfo($displayName, PATHINFO_EXTENSION));

        return Response::make($binary, 200, [
            'Content-Type' => $this->mimeFromExtension($ext),
            'Content-Disposition' => 'attachment; filename="' . $this->escapeContentDispositionFilename($displayName) . '"',
            'Content-Length' => strlen($binary),
        ]);
    }

    /**
     * Download all attachments for an email as ZIP (same attachment set as the list UI: non-inline rows).
     */
    public function downloadAll($mailReportId)
    {
        try {
            $mailReport = Email::findOrFail($mailReportId);
            // Match Emails tab list: include all stored attachment rows (same as UI attachment list)
            $attachments = $mailReport->attachments()->orderBy('id')->get();

            if ($attachments->isEmpty()) {
                abort(404, 'No attachments found');
            }

            $zipFileName = 'attachments_' . $mailReportId . '_' . time() . '.zip';
            $zipPath = storage_path('app/temp/' . $zipFileName);

            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }

            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE) !== true) {
                abort(500, 'Could not create ZIP file');
            }

            $added = 0;
            $usedNames = [];
            foreach ($attachments as $attachment) {
                try {
                    $binary = $this->resolveAttachmentBinary($attachment);
                    if ($binary === null || $binary === '') {
                        continue;
                    }
                    $entryName = $this->uniqueZipEntryName($this->downloadDisplayFilenameForAttachment($attachment), $usedNames);
                    $zip->addFromString($entryName, $binary);
                    $added++;
                } catch (\Exception $e) {
                    Log::warning('Failed to add attachment to ZIP', [
                        'attachment_id' => $attachment->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $zip->close();

            if ($added === 0) {
                @unlink($zipPath);
                abort(404, 'No downloadable attachment files');
            }

            return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'Email not found');
        } catch (\Exception $e) {
            Log::error('Download all attachments failed', [
                'mail_report_id' => $mailReportId,
                'error' => $e->getMessage(),
            ]);
            abort(500, 'Failed to create ZIP file');
        }
    }

    /**
     * Preview attachment (for images/PDFs)
     */
    public function preview($id)
    {
        try {
            $attachment = MailReportAttachment::findOrFail($id);

            if (!$attachment->canPreview()) {
                abort(400, 'This file type cannot be previewed');
            }

            $binary = $this->resolveAttachmentBinary($attachment);
            if ($binary === null) {
                abort(404, 'Attachment file not found');
            }

            $mimeType = $attachment->getEffectiveMimeType();

            $displayName = $this->downloadDisplayFilenameForAttachment($attachment);

            return Response::make($binary, 200, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . $this->escapeContentDispositionFilename($displayName) . '"',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'Attachment not found');
        } catch (\Exception $e) {
            Log::error('Attachment preview failed', ['id' => $id, 'error' => $e->getMessage()]);
            abort(404, 'Attachment file not found');
        }
    }

    /**
     * Resolve file bytes for a MailReportAttachment row.
     */
    protected function resolveAttachmentBinary(MailReportAttachment $attachment): ?string
    {
        try {
            if ($this->s3Configured()) {
                $disk = Storage::disk('s3');
                if (!empty($attachment->s3_key) && $disk->exists($attachment->s3_key)) {
                    return $disk->get($attachment->s3_key);
                }
                $keyFromUrl = $this->extractS3KeyFromPublicUrl($attachment->file_path);
                if ($keyFromUrl && $disk->exists($keyFromUrl)) {
                    return $disk->get($keyFromUrl);
                }
            }
        } catch (\Exception $e) {
            Log::warning('S3 resolve failed for attachment', [
                'id' => $attachment->id,
                'error' => $e->getMessage(),
            ]);
        }

        $fp = $attachment->file_path;
        if (is_string($fp) && $fp !== '' && !preg_match('#^https?://#i', $fp)) {
            if (@is_file($fp)) {
                $data = @file_get_contents($fp);
                return $data !== false ? $data : null;
            }
        }

        return null;
    }

    /**
     * Legacy JSON file_url → bytes (local path or S3 via URL/key).
     */
    protected function resolveBinaryFromLegacyFileUrl(string $fileUrl): ?string
    {
        if ($fileUrl === '') {
            return null;
        }
        if (preg_match('#^https?://#i', $fileUrl)) {
            try {
                if ($this->s3Configured()) {
                    $key = $this->extractS3KeyFromPublicUrl($fileUrl);
                    if ($key) {
                        $disk = Storage::disk('s3');
                        if ($disk->exists($key)) {
                            return $disk->get($key);
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Legacy S3 resolve failed', ['url' => $fileUrl, 'error' => $e->getMessage()]);
            }
            return null;
        }

        if (@is_file($fileUrl)) {
            $data = @file_get_contents($fileUrl);
            return $data !== false ? $data : null;
        }

        $norm = str_replace('\\', '/', $fileUrl);
        $base = basename($norm);
        $candidates = array_unique(array_filter([
            public_path('checklists/' . $base),
            public_path('checklists/' . ltrim($norm, '/')),
            public_path('img/documents/' . $base),
        ]));
        foreach ($candidates as $path) {
            if (@is_file($path)) {
                $data = @file_get_contents($path);
                if ($data !== false) {
                    return $data;
                }
            }
        }

        return null;
    }

    protected function s3Configured(): bool
    {
        return !empty(config('filesystems.disks.s3.key')) && !empty(config('filesystems.disks.s3.bucket'));
    }

    /**
     * Derive S3 object key from a public URL when s3_key column is empty.
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

    /**
     * Safe basename for Content-Disposition (ASCII-oriented; strips Windows-invalid chars).
     */
    protected function safeFilename(?string $name): string
    {
        $name = $name ?: 'file';
        $name = str_replace(['"', "\r", "\n"], '', basename($name));
        $name = preg_replace('/[\/\\\\?%*:|"<>\\[\\]]+/u', '-', $name);
        $name = preg_replace('/\s+/u', '_', $name);
        $name = substr($name, 0, 200);
        return $name !== '' ? $name : 'file';
    }

    protected function escapeContentDispositionFilename(string $name): string
    {
        return str_replace(['\\', '"'], ['\\\\', '\\"'], $name);
    }

    /**
     * Filename with extension for downloads (aligns with emails_v2.js resolveAttachmentDownloadFilename).
     */
    protected function downloadDisplayFilenameForAttachment(MailReportAttachment $attachment): string
    {
        $raw = $attachment->filename ? trim($attachment->filename) : 'file';
        $name = $this->safeFilename($raw);

        $ext = strtolower(trim((string) ($attachment->extension ?? ''), ". \t\n\r\0\x0B"));
        if ($ext !== '' && !preg_match('/^[a-zA-Z0-9]{1,8}$/', $ext)) {
            $ext = '';
        }
        if ($ext === '' && !empty($attachment->content_type)) {
            $ext = $this->extensionFromMimeType($attachment->content_type);
        }
        if ($ext === '' && !empty($attachment->s3_key)) {
            $ext = strtolower(pathinfo($attachment->s3_key, PATHINFO_EXTENSION));
        }
        if ($ext === '' && !empty($attachment->file_path)) {
            if (preg_match('#^https?://#i', $attachment->file_path)) {
                $path = parse_url($attachment->file_path, PHP_URL_PATH);
                $ext = $path ? strtolower(pathinfo($path, PATHINFO_EXTENSION)) : '';
            } else {
                $ext = strtolower(pathinfo($attachment->file_path, PATHINFO_EXTENSION));
            }
        }

        if ($ext !== '' && !preg_match('/\.' . preg_quote($ext, '/') . '$/i', $name)) {
            $name .= '.' . $ext;
        }

        return $name;
    }

    /**
     * @param  array<string, mixed>  $item
     */
    protected function downloadDisplayFilenameForLegacyItem(array $item, string $fileUrl): string
    {
        $name = $item['file_name'] ?? '';
        if ($name === '') {
            $name = $fileUrl !== '' ? basename(str_replace('\\', '/', $fileUrl)) : 'download';
        }
        $name = $this->safeFilename($name);

        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        if ($ext === '' && $fileUrl !== '') {
            $ext = strtolower(pathinfo(str_replace('\\', '/', $fileUrl), PATHINFO_EXTENSION));
        }
        if ($ext === '' && $fileUrl !== '' && preg_match('#^https?://#i', $fileUrl)) {
            $path = parse_url($fileUrl, PHP_URL_PATH);
            if ($path) {
                $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            }
        }

        if ($ext !== '' && !preg_match('/\.' . preg_quote($ext, '/') . '$/i', $name)) {
            $name .= '.' . $ext;
        }

        return $name;
    }

    protected function extensionFromMimeType(?string $mime): string
    {
        $mime = strtolower(trim((string) ($mime ?? ''), ' '));
        if ($mime === '') {
            return '';
        }
        $mime = explode(';', $mime)[0];

        $map = [
            'application/pdf' => 'pdf',
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.ms-excel' => 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
            'text/plain' => 'txt',
            'text/csv' => 'csv',
        ];

        return $map[$mime] ?? '';
    }

    protected function mimeFromExtension(string $ext): string
    {
        $ext = strtolower($ext);
        $map = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'txt' => 'text/plain',
            'csv' => 'text/csv',
        ];

        return $map[$ext] ?? 'application/octet-stream';
    }

    /**
     * Avoid duplicate names inside a ZIP when filenames collide.
     */
    protected function uniqueZipEntryName(string $filename, array &$usedNames): string
    {
        $base = basename($filename) ?: 'file';
        if (!isset($usedNames[$base])) {
            $usedNames[$base] = 1;
            return $base;
        }
        $usedNames[$base]++;
        $path = pathinfo($base, PATHINFO_FILENAME);
        $ext = pathinfo($base, PATHINFO_EXTENSION);
        $suffix = $usedNames[$base];
        return $ext !== '' ? "{$path}_{$suffix}.{$ext}" : "{$base}_{$suffix}";
    }
}
