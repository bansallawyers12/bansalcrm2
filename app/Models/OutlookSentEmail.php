<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OutlookSentEmail extends Model
{
    protected $table = 'outlook_sent_emails';

    protected $fillable = [
        'from_email',
        'to_email',
        'cc',
        'subject',
        'body',
        'sent_at',
        'admin_id',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}
