<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffFileTimeEntry extends Model
{
    public const KIND_PRISMS = 'prisms';

    public const KIND_PROVIDER_PORTAL = 'provider_portal';

    public const KIND_MAILBOX = 'mailbox';

    public const KIND_DRAFT = 'draft';

    public const KIND_INTERNAL = 'internal';

    public const KIND_OTHER = 'other';

    public const STATUS_DONE = 'done';

    public const ACTIVITY_TYPE = 'file_time';

    /**
     * @return list<string>
     */
    public static function kinds(): array
    {
        return [
            self::KIND_PRISMS,
            self::KIND_PROVIDER_PORTAL,
            self::KIND_MAILBOX,
            self::KIND_DRAFT,
            self::KIND_INTERNAL,
            self::KIND_OTHER,
        ];
    }

    protected $fillable = [
        'staff_id',
        'record_type',
        'record_id',
        'application_id',
        'kind',
        'title',
        'status',
        'is_running',
        'clock_seconds',
        'confirmed_minutes',
        'started_at',
        'completed_at',
        'activities_log_id',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => self::STATUS_DONE,
        'is_running' => false,
        'clock_seconds' => 0,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_running' => 'boolean',
            'clock_seconds' => 'integer',
            'confirmed_minutes' => 'integer',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'application_id');
    }

    public function activitiesLog(): BelongsTo
    {
        return $this->belongsTo(ActivitiesLog::class, 'activities_log_id');
    }

    public function isAdmin(): bool
    {
        return $this->record_type === null || $this->record_id === null;
    }

    public function isPosted(): bool
    {
        return $this->activities_log_id !== null;
    }

    public function kindLabel(): string
    {
        return match ($this->kind) {
            self::KIND_PRISMS => 'PRISMS',
            self::KIND_PROVIDER_PORTAL => 'provider portal',
            self::KIND_MAILBOX => 'mailbox',
            self::KIND_DRAFT => 'drafting',
            self::KIND_INTERNAL => 'internal',
            self::KIND_OTHER => 'other',
            default => (string) $this->kind,
        };
    }
}
