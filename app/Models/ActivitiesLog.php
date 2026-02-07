<?php

namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use App\Models\SmsLog;
use Illuminate\Database\Eloquent\Model;

class ActivitiesLog extends Model
{	
    use Sortable;
    
    protected $table = 'activities_logs';
    
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
    ];
    
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
