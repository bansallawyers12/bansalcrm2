<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FollowupConsultant extends Model
{
    protected $table = 'followup_consultants';

    protected $fillable = [
        'slug',
        'name',
        'sort_order',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
