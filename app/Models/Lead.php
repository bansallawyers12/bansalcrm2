<?php
namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{	use Sortable;

	protected $fillable = [
        'id', 'name', 'status', 'created_at', 'updated_at'
    ];
	
	public $sortable = ['id', 'name', 'created_at', 'updated_at'];
	
	public function user()
    {
        return $this->belongsTo('App\Models\User','user_id','id');
    }
	
	public function agentdetail()
    {
        return $this->belongsTo('App\Models\User','agent_id','id');
    }
	
	public function staffuser()
    {
        return $this->belongsTo('App\Models\Admin','assign_to','id');
    }
	public function followupload()
    {
        return $this->belongsTo('App\Models\Followup','id','lead_id');
    }
    public function likes()
    {
        return $this->hasMany('App\Models\Followup','id');
    }
    
    /**
     * Get the preferredIntake attribute (maps to preferredintake column)
     *
     * @param  mixed  $value
     * @return mixed
     */
    public function getPreferredIntakeAttribute($value)
    {
        // Map preferredIntake to preferredintake column
        return $this->attributes['preferredintake'] ?? null;
    }
    
    /**
     * Set the preferredIntake attribute (maps to preferredintake column)
     *
     * @param  mixed  $value
     * @return void
     */
    public function setPreferredIntakeAttribute($value)
    {
        // Map preferredIntake to preferredintake column
        // PostgreSQL doesn't accept empty strings for date columns - convert to NULL
        $this->attributes['preferredintake'] = ($value === '' || $value === null) ? null : $value;
    }
   
}