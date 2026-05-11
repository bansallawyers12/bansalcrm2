<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EliteEmail extends Model
{
    protected $table = 'elite_emails';

    protected $fillable = [
        'from_address',
        'to_address',
        'subject',
        'body_text',
        'body_html',
        'body_html_s3_key',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function attachments(): HasMany
    {
        return $this->hasMany(EliteEmailAttachment::class, 'elite_email_id');
    }
}
