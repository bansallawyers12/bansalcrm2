<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FollowupCalendarBlockTiming extends Model
{
    protected $table = 'followup_calendar_block_timings';

    public const BLOCK_TYPES = [
        'unavailable' => 'Unavailable',
        'busy' => 'Busy',
    ];

    public const RECURRENCE = [
        'none' => 'No Recurrence',
        'daily' => 'Daily',
        'weekly' => 'Weekly',
        'monthly' => 'Monthly',
    ];

    /** @var array<string, string> slug => short label (matches followup_consultants.slug) */
    public const CONSULTANT_SLUG_OPTIONS = [
        'ankit' => 'Ankit',
        'rakshita' => 'Rakshita',
        'jaspreet' => 'Jaspreet',
        'syed' => 'Syed',
    ];

    protected $fillable = [
        'title',
        'block_date',
        'is_all_day',
        'start_time',
        'end_time',
        'block_type',
        'recurrence',
        'locations',
        'calendar_types',
        'consultant_slugs',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'block_date' => 'date',
            'is_all_day' => 'boolean',
            'is_active' => 'boolean',
            'locations' => 'array',
            'calendar_types' => 'array',
            'consultant_slugs' => 'array',
        ];
    }
}
