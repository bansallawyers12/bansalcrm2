<?php
namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MailReport extends Model
{	
    use Sortable;

	protected $fillable = [
        'id',
        'user_id',
        'from_mail',
        'to_mail',
        'cc',
        'template_id',
        'subject',
        'message',
        'type',
        'reciept_id',
        'attachments',
        'mail_type',
        'client_id',
        'client_matter_id',
        'conversion_type',
        'mail_body_type',
        'fetch_mail_sent_time',
        'uploaded_doc_id',
        'mail_is_read',
        // Python analysis fields (v2 additions)
        'python_analysis',
        'python_rendering',
        'sentiment',
        'language',
        'enhanced_html',
        'rendered_html',
        'text_preview',
        'security_issues',
        'thread_info',
        'processed_at',
        // Additional metadata
        'message_id',
        'thread_id',
        'received_date',
        'last_accessed_at',
        'file_hash',
        'category',
        'priority',
        'created_at',
        'updated_at'
    ];
	
	public $sortable = ['id', 'created_at', 'updated_at', 'subject', 'from_mail'];

    protected $casts = [
        'python_analysis' => 'array',
        'python_rendering' => 'array',
        'security_issues' => 'array',
        'thread_info' => 'array',
        'processed_at' => 'datetime',
        'received_date' => 'datetime',
        'last_accessed_at' => 'datetime',
        'mail_is_read' => 'boolean',
    ];

    /**
     * Get the attachments for the email.
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(MailReportAttachment::class, 'mail_report_id');
    }

    /**
     * Get the labels for the email.
     */
    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(EmailLabel::class, 'email_label_mail_report', 'mail_report_id', 'email_label_id')
                    ->withTimestamps();
    }

    /**
     * Get the client that owns the email.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'client_id');
    }

    /**
     * Get the user who uploaded the email.
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'user_id');
    }

    /**
     * Check if the email has attachments.
     */
    public function hasAttachments(): bool
    {
        return $this->attachments()->count() > 0;
    }

    /**
     * Get the number of attachments.
     */
    public function getAttachmentCountAttribute(): int
    {
        return $this->attachments()->count();
    }

    /**
     * Scope to filter by label.
     */
    public function scopeWithLabel($query, $labelId)
    {
        return $query->whereHas('labels', function ($q) use ($labelId) {
            $q->where('email_labels.id', $labelId);
        });
    }
}