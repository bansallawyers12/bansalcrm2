<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffDaySummary extends Model
{
    public const SOURCE_COPY = 'copy';

    public const SOURCE_SCHEDULE = 'schedule';

    protected $fillable = [
        'staff_id',
        'summary_date',
        'body',
        'source',
        'saved_at',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'source' => self::SOURCE_COPY,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'summary_date' => 'date',
            'saved_at' => 'datetime',
        ];
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }
}
