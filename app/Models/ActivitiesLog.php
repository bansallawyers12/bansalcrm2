<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Kyslik\ColumnSortable\Sortable;

class ActivitiesLog extends BaseModel
{
    use Sortable;

    protected $table = 'activities_logs';

    public const TASK_GROUP_PARTNER = 'partner';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'client_id',
        'created_by',
        'subject',
        'description',
        'use_for',
        'task_status',
        'pin',
        'sms_log_id',
        'activity_type',
        'task_group',
    ];

    /**
     * Partner rows reuse client_id for partners.id. Keep student queries off those rows
     * so a matching admins.id (e.g. partner 5910 / client 5910) is not treated as student work.
     */
    public function scopeForStudentRecords(Builder $query): Builder
    {
        $column = $this->getTable().'.task_group';

        return $query->where(function (Builder $q) use ($column): void {
            $q->whereNull($column)
                ->orWhere($column, '!=', self::TASK_GROUP_PARTNER);
        });
    }

    /**
     * Get the client associated with this activity
     */
    public function client()
    {
        return $this->belongsTo('App\Models\Admin', 'client_id', 'id');
    }

    /**
     * Get the user who created this activity
     */
    public function createdBy()
    {
        return $this->belongsTo('App\Models\Admin', 'created_by', 'id');
    }

    /**
     * Get the SMS log if this activity is SMS-related
     */
    public function smsLog()
    {
        return $this->belongsTo(SmsLog::class, 'sms_log_id');
    }
}
