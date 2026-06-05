<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FollowupCalendarSetting extends Model
{
    protected $table = 'followup_calendar_settings';

    protected $fillable = [
        'followup_consultant_id',
        'service_type',
        'start_time',
        'end_time',
        'slot_duration_minutes',
        'available_days',
        'is_active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'slot_duration_minutes' => 'integer',
            'available_days' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function consultant(): BelongsTo
    {
        return $this->belongsTo(FollowupConsultant::class, 'followup_consultant_id');
    }
}
