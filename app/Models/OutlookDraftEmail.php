<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OutlookDraftEmail extends Model
{
    protected $table = 'outlook_draft_emails';

    protected $fillable = [
        'from_email',
        'to_email',
        'cc',
        'subject',
        'body',
        'admin_id',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}
