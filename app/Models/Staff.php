<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;

class Staff extends Authenticatable
{
    use Notifiable, Sortable;

    protected $guard = 'admin';
    protected $table = 'staff';

    /**
     * Staff table columns (telephone, att_* removed).
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password',
        'country_code', 'phone',
        'status', 'verified',
        'role', 'position', 'team', 'permission', 'office_id',
        'show_dashboard_per', 'time_zone',
        'email_signature',
        'quick_access_enabled',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'status' => 'integer',
        'verified' => 'integer',
        'show_dashboard_per' => 'integer',
        'quick_access_enabled' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public $sortable = [
        'id', 'first_name', 'last_name', 'email', 'status', 'created_at', 'updated_at',
    ];

    public function office(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'office_id');
    }

    public function usertype(): BelongsTo
    {
        return $this->belongsTo(StaffRole::class, 'role', 'id');
    }

    public function getFullNameAttribute(): string
    {
        return trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? '')) ?: ($this->email ?? '');
    }

    /**
     * Formatted phone (phone + country_code).
     */
    public function getFormattedPhoneAttribute(): string
    {
        return \App\Helpers\PhoneHelper::formatPhoneNumber(
            $this->attributes['country_code'] ?? '',
            $this->attributes['phone'] ?? ''
        );
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
