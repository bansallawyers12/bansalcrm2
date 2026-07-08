<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @method static static|null find($id, $columns = null)
 * @method static \Illuminate\Database\Eloquent\Builder where($column, $operator = null, $value = null, $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder select(array|string|null $columns = null)
 */
class MailReportAttachment extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'mail_report_id',
        'filename',
        'display_name',
        'content_type',
        'file_path',
        's3_key',
        'file_size',
        'content_id',
        'is_inline',
        'description',
        'headers',
        'extension',
    ];

    protected $casts = [
        'is_inline' => 'boolean',
        'headers' => 'array',
    ];

    /**
     * Get the mail report that owns the attachment.
     */
    public function mailReport(): BelongsTo
    {
        return $this->belongsTo(Email::class);
    }

    /**
     * Get the formatted file size.
     */
    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get the display name or filename.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->attributes['display_name'] ?? $this->filename;
    }

    /**
     * Check if the attachment is an image.
     * Checks content_type first, then falls back to file extension for generic types.
     */
    public function isImage(): bool
    {
        $imageTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/bmp', 'image/webp'];
        
        // First check by content type
        if (in_array($this->content_type, $imageTypes)) {
            return true;
        }
        
        // Fallback: check by extension if content_type is generic (application/octet-stream)
        if ($this->content_type === 'application/octet-stream' || empty($this->content_type)) {
            $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
            return in_array($this->getFileExtension(), $imageExtensions);
        }
        
        return false;
    }

    /**
     * Check if the attachment is a PDF.
     * Checks content_type first, then falls back to file extension for generic types.
     */
    public function isPdf(): bool
    {
        // First check by content type
        if ($this->content_type === 'application/pdf') {
            return true;
        }
        
        // Fallback: check by extension if content_type is generic
        if ($this->content_type === 'application/octet-stream' || empty($this->content_type)) {
            return $this->getFileExtension() === 'pdf';
        }
        
        return false;
    }

    /**
     * Get the lowercase file extension from filename.
     */
    protected function getFileExtension(): string
    {
        $extension = $this->extension ?? pathinfo($this->filename ?? '', PATHINFO_EXTENSION);
        return strtolower($extension ?? '');
    }

    /**
     * Get the effective MIME type (with fallback for generic types).
     * Useful for serving files with correct Content-Type header.
     */
    public function getEffectiveMimeType(): string
    {
        // If content_type is specific, use it
        if ($this->content_type && $this->content_type !== 'application/octet-stream') {
            return $this->content_type;
        }
        
        // Map common extensions to MIME types
        $mimeMap = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'webp' => 'image/webp',
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'txt' => 'text/plain',
            'csv' => 'text/csv',
            'html' => 'text/html',
            'json' => 'application/json',
            'xml' => 'application/xml',
        ];
        
        $ext = $this->getFileExtension();
        return $mimeMap[$ext] ?? 'application/octet-stream';
    }

    /**
     * Check if the attachment is a document.
     */
    public function isDocument(): bool
    {
        $documentTypes = [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'text/plain',
            'application/rtf',
            'text/html',
            'text/css',
            'application/json',
            'application/xml',
            'text/csv',
        ];
        return in_array($this->content_type, $documentTypes);
    }

    /**
     * Check if the attachment can be previewed.
     */
    public function canPreview(): bool
    {
        return $this->isImage() || $this->isPdf();
    }

    /**
     * CSS classes for the attachment icon (legacy callers).
     */
    public function getIconClassAttribute(): string
    {
        $name = 'paperclip';
        $extra = 'attachment-icon text-gray-400';

        if ($this->isImage()) {
            $name = 'file-image';
            $extra = 'attachment-icon attachment-icon-image text-blue-500';
        } elseif ($this->isPdf()) {
            $name = 'file-pdf';
            $extra = 'attachment-icon attachment-icon-pdf text-red-500';
        } elseif ($this->isDocument()) {
            $name = 'file-alt';
            $extra = 'attachment-icon attachment-icon-word text-gray-500';
        }

        return \App\Helpers\IconHelper::classes($name, 'solid', ['class' => $extra]);
    }

    /**
     * Rendered Lucide icon HTML for Blade templates.
     */
    public function getIconHtmlAttribute(): string
    {
        if ($this->isImage()) {
            return \App\Helpers\IconHelper::render('file-image', 'solid', ['class' => 'attachment-icon attachment-icon-image text-blue-500']);
        }

        if ($this->isPdf()) {
            return \App\Helpers\IconHelper::render('file-pdf', 'solid', ['class' => 'attachment-icon attachment-icon-pdf text-red-500']);
        }

        if ($this->isDocument()) {
            return \App\Helpers\IconHelper::render('file-alt', 'solid', ['class' => 'attachment-icon attachment-icon-word text-gray-500']);
        }

        return \App\Helpers\IconHelper::render('paperclip', 'solid', ['class' => 'attachment-icon text-gray-400']);
    }

    /**
     * Scope to filter by content type.
     */
    public function scopeOfType(Builder $query, string $contentType): Builder
    {
        return $query->where('content_type', $contentType);
    }

    /**
     * Scope to filter inline attachments.
     */
    public function scopeInline(Builder $query): Builder
    {
        return $query->where('is_inline', true);
    }

    /**
     * Scope to filter regular attachments.
     */
    public function scopeRegular(Builder $query): Builder
    {
        return $query->where('is_inline', false);
    }
}
