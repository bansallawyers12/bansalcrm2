<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VerifiedNumber extends Model
{
    protected $fillable = [
        'phone_number',
        'is_verified',
        'verification_code',
        'verified_at'
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
    ];
}
