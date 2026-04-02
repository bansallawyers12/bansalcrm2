<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EliteEmail extends Model
{
    protected $table = 'elite_emails';

    protected $fillable = [
        'from_address',
        'to_address',
        'subject',
        'body_text',
        'body_html',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];
}
