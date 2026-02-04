<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientOngoingReference extends Model
{
    protected $table = 'client_ongoing_references';

    protected $fillable = [
        'client_id',
        'current_status',
        'payment_display_note',
        'institute_override',
        'visa_category_override',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot method to auto-set created_by and updated_by
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (auth()->guard('admin')->check() && !$model->created_by) {
                $model->created_by = auth()->guard('admin')->id();
            }
        });

        static::updating(function ($model) {
            if (auth()->guard('admin')->check()) {
                $model->updated_by = auth()->guard('admin')->id();
            }
        });
    }

    /**
     * Relationship to client (Admin)
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'client_id');
    }

    /**
     * Relationship to creator (Admin)
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    /**
     * Relationship to updater (Admin)
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'updated_by');
    }
}
