<?php

namespace App\Services;

use App\Models\EliteEmail;
use App\Models\EliteEmailAttachment;
use Illuminate\Contracts\Filesystem\Filesystem;
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
     * Disk name used for new inbound Elite attachments (see config/crm.php).
     */
    public static function writeDisk(): string
    {
        $d = (string) config('crm.education_elite_inbound_attachments_disk', 's3');

        return array_key_exists($d, config('filesystems.disks', [])) ? $d : 'local';
    }

    /**
     * Resolve a filesystem where this storage_path already exists (new S3 vs legacy local).
     */
    public static function findDiskForPath(string $storagePath): ?Filesystem
    {
        if ($storagePath === '') {
            return null;
        }

        foreach (array_unique([self::writeDisk(), 'local']) as $name) {
            if (! is_string($name) || ! array_key_exists($name, config('filesystems.disks', []))) {
                continue;
            }
            $disk = Storage::disk($name);
            if ($disk->exists($storagePath)) {
                return $disk;
            }
        }

        return null;
    }

    /**
     * Persist multipart files from inbound webhook posts (attachment1, attachment2, …).
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
        $objectBasename = $uniquePrefix.'_'.$diskName;
        $writeDisk = self::writeDisk();
        $storedPath = $file->storeAs($relativeDir, $objectBasename, $writeDisk);
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

        $size = (int) ($file->getSize() ?: 0);
        if ($size === 0) {
            try {
                $size = (int) Storage::disk($writeDisk)->size($storedPath);
            } catch (\Throwable $e) {
                $size = 0;
            }
        }

        EliteEmailAttachment::create([
            'elite_email_id' => $email->id,
            'form_field' => $field,
            'original_filename' => substr($basename, 0, 500),
            'mime_type' => substr($mime, 0, 250),
            'size_bytes' => $size,
            'content_id' => $contentId !== null && $contentId !== '' ? substr($contentId, 0, 500) : null,
            'storage_path' => $storedPath,
        ]);
    }
}
