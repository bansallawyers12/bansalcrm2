<?php
namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CheckinLog extends Model
{
    use Sortable;

	protected $fillable = [
        'id', 'client_id', 'user_id', 'visit_purpose', 'office',
        'contact_type', 'status', 'date', 'sesion_start', 'sesion_end',
        'wait_time', 'attend_time', 'wait_type',
        'created_at', 'updated_at'
    ];
	
	public $sortable = ['id', 'created_at', 'updated_at'];

    /**
     * Get the office (branch) where this check-in occurred.
     * Column is 'office' (not office_id) - stores branch id.
     */
    public function office(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'office', 'id');
    }
	
	/**
     * Get the client for this check-in
     */
    public function client()
    {
        if ($this->contact_type == 'Lead') {
            return $this->belongsTo('App\Models\Lead', 'client_id');
        } else {
            return $this->belongsTo('App\Models\Admin', 'client_id')->where('role', '7');
        }
    }
    
    /**
     * Get the assignee for this check-in
     */
    public function assignee()
    {
        return $this->belongsTo('App\Models\Admin', 'user_id');
    }
    
    /**
     * Get the history entries for this check-in
     */
    public function histories()
    {
        return $this->hasMany('App\Models\CheckinHistory', 'checkin_id');
    }
}