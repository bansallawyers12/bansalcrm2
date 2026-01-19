<?php

namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
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

}
