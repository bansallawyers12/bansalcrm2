<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EliteEmailAttachment extends Model
{
    protected $table = 'elite_email_attachments';

    protected $fillable = [
        'elite_email_id',
        'form_field',
        'original_filename',
        'mime_type',
        'size_bytes',
        'content_id',
        'storage_path',
    ];

    public function eliteEmail(): BelongsTo
    {
        return $this->belongsTo(EliteEmail::class, 'elite_email_id');
    }
}
