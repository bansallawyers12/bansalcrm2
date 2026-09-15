<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffFileTimeEntry extends Model
{
    /** Note-style kinds (match client/partner note title select). */
    public const KIND_CALL = 'call';

    public const KIND_EMAIL = 'email';

    public const KIND_IN_PERSON = 'in_person';

    public const KIND_OTHERS = 'others';

    public const KIND_ATTENTION = 'attention';

    /** Off-CRM extras beyond note titles. */
    public const KIND_SMS = 'sms';

    public const KIND_PROVIDER_PORTAL = 'provider_portal';

    public const KIND_DRAFT = 'draft';

    public const KIND_INTERNAL = 'internal';

    public const KIND_STAFF_MEETING = 'staff_meeting';

    public const STATUS_DONE = 'done';

    public const ACTIVITY_TYPE = 'file_time';

    /**
     * @return list<string>
     */
    public static function kinds(): array
    {
        return [
            self::KIND_CALL,
            self::KIND_EMAIL,
            self::KIND_IN_PERSON,
            self::KIND_OTHERS,
            self::KIND_ATTENTION,
            self::KIND_SMS,
            self::KIND_PROVIDER_PORTAL,
            self::KIND_DRAFT,
            self::KIND_INTERNAL,
            self::KIND_STAFF_MEETING,
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
            self::KIND_CALL => 'Call',
            self::KIND_EMAIL => 'Email',
            self::KIND_IN_PERSON => 'In-Person',
            self::KIND_OTHERS => 'Others',
            self::KIND_ATTENTION => 'Attention',
            self::KIND_SMS => 'SMS',
            self::KIND_PROVIDER_PORTAL => 'Provider portal',
            self::KIND_DRAFT => 'Drafting',
            self::KIND_INTERNAL => 'Internal',
            self::KIND_STAFF_MEETING => 'Staff meeting',
            // Legacy v1 presets (still render if present in older rows)
            'prisms' => 'PRISMS',
            'mailbox' => 'Mailbox',
            'other' => 'Other',
            default => (string) $this->kind,
        };
    }
}
