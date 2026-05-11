<?php

namespace App\Services;

use App\Models\EliteEmail;
use App\Models\EliteEmailAttachment;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class EliteInboundAttachmentService
{
    /**
     * Persist multipart files from SendGrid Inbound Parse (attachment1, attachment2, …).
     */
    public function storeFromInboundRequest(EliteEmail $email, Request $request): void
    {
        if (! Schema::hasTable('elite_email_attachments')) {
            return;
        }

        $info = $this->parseAttachmentInfo($request);

        foreach ($request->allFiles() as $field => $uploaded) {
            if (! $this->isInboundAttachmentField((string) $field)) {
                continue;
            }

            $files = is_array($uploaded) ? $uploaded : [$uploaded];
            foreach ($files as $file) {
                if (! $file instanceof UploadedFile || ! $file->isValid()) {
                    continue;
                }

                try {
                    $this->storeOneFile($email, (string) $field, $file, $info[$field] ?? ($info[(string) $field] ?? []));
                } catch (Throwable $e) {
                    Log::warning('elite.inbound.attachment_file_failed', [
                        'elite_email_id' => $email->id,
                        'field' => $field,
                        'message' => $e->getMessage(),
                    ]);
                }
            }
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function parseAttachmentInfo(Request $request): array
    {
        $raw = $request->input('attachment-info');
        if (! is_string($raw) || trim($raw) === '') {
            return [];
        }

        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : [];
    }

    private function isInboundAttachmentField(string $field): bool
    {
        if ($field === 'attachment') {
            return true;
        }

        return (bool) preg_match('/^attachment\d+$/i', $field);
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    private function storeOneFile(EliteEmail $email, string $field, UploadedFile $file, array $meta): void
    {
        $original = $file->getClientOriginalName() ?: 'attachment';
        $original = trim(str_replace(["\0", "\r", "\n"], '', $original));
        if ($original === '') {
            $original = 'attachment';
        }

        $basename = basename($original);
        $safeStem = Str::slug(pathinfo($basename, PATHINFO_FILENAME)) ?: 'file';
        $ext = pathinfo($basename, PATHINFO_EXTENSION);
        $diskName = Str::lower($safeStem).($ext !== '' ? '.'.$ext : '');
        $diskName = substr($diskName, 0, 200);
        $uniquePrefix = bin2hex(random_bytes(6));

        $relativeDir = 'elite-inbound/'.$email->id;
        $storedPath = $file->storeAs($relativeDir, $uniquePrefix.'_'.$diskName, 'local');
        if ($storedPath === false) {
            throw new \RuntimeException('Failed to store attachment');
        }

        $mime = $meta['type'] ?? $meta['content-type'] ?? $meta['Content-Type'] ?? null;
        if (! is_string($mime) || $mime === '') {
            $mime = $file->getMimeType() ?: $file->getClientMimeType() ?: 'application/octet-stream';
        }

        $contentId = $meta['content-id'] ?? $meta['content_id'] ?? $meta['Content-ID'] ?? null;
        if (is_string($contentId)) {
            $contentId = trim(strtolower($contentId), "<> \t\r\n");
        } else {
            $contentId = null;
        }

        EliteEmailAttachment::create([
            'elite_email_id' => $email->id,
            'form_field' => $field,
            'original_filename' => substr($basename, 0, 500),
            'mime_type' => substr($mime, 0, 250),
            'size_bytes' => (int) ($file->getSize() ?: Storage::disk('local')->size($storedPath)),
            'content_id' => $contentId !== null && $contentId !== '' ? substr($contentId, 0, 500) : null,
            'storage_path' => $storedPath,
        ]);
    }
}
