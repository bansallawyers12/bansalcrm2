<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientEmail extends Model
{
    protected $table = 'client_emails';

    protected $fillable = [
        'client_id',
        'user_id',
        'email_type',
        'client_email',
        'is_verified',
        'verified_at',
        'verified_by',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(\App\Models\Admin::class, 'client_id');
    }
}
