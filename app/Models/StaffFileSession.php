<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffFileSession extends Model
{
    public const STATUS_ACCESSED = 'accessed';

    public const STATUS_RECORDED = 'recorded';

    public const STATUS_CLOSED = 'closed';

    public const RECORD_TYPE_STUDENT = 'student';

    public const RECORD_TYPE_PARTNER = 'partner';

    public const ACTIVITY_TYPE = 'file_time';

    protected $fillable = [
        'staff_id',
        'record_type',
        'record_id',
        'application_id',
        'application_key',
        'session_date',
        'status',
        'focused_seconds',
        'idle_cut_seconds',
        'confirmed_minutes',
        'event_count',
        'is_reviewed_only',
        'started_at',
        'last_heartbeat_at',
        'ended_at',
        'activities_log_id',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => self::STATUS_ACCESSED,
        'focused_seconds' => 0,
        'idle_cut_seconds' => 0,
        'application_key' => 0,
        'is_reviewed_only' => false,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'session_date' => 'date',
            'is_reviewed_only' => 'boolean',
            'started_at' => 'datetime',
            'last_heartbeat_at' => 'datetime',
            'ended_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (StaffFileSession $session): void {
            $session->application_key = (int) ($session->application_id ?? 0);
        });
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'application_id');
    }

    public function activitiesLog(): BelongsTo
    {
        return $this->belongsTo(ActivitiesLog::class, 'activities_log_id');
    }

    public function isPartner(): bool
    {
        return $this->record_type === self::RECORD_TYPE_PARTNER;
    }

    public function isRecordedForBoard(): bool
    {
        if ($this->status === self::STATUS_RECORDED) {
            return true;
        }

        if ($this->status !== self::STATUS_CLOSED) {
            return false;
        }

        return (bool) $this->is_reviewed_only
            || (int) ($this->event_count ?? 0) > 0
            || $this->activities_log_id !== null;
    }
}
