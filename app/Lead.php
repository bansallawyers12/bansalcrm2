<?php
namespace App;

use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Lead extends Authenticatable
{
    use Notifiable;
	use Sortable;

	protected $fillable = [
        'id', 'name', 'status', 'created_at', 'updated_at'
    ];
	
	public $sortable = ['id', 'name', 'created_at', 'updated_at'];
	
	public function package_detail()
    {
        return $this->belongsTo('App\Package','package_id','id');
    }
	
	public function user()
    {
        return $this->belongsTo('App\User','user_id','id');
    }
	
	public function agentdetail()
    {
        return $this->belongsTo('App\User','agent_id','id');
    }
	
	public function staffuser()
    {
        return $this->belongsTo('App\Admin','assign_to','id');
    }
	public function followupload()
    {
        return $this->belongsTo('App\Followup','id','lead_id');
    }
    public function likes()
    {
        return $this->hasMany('App\Followup','id');
    } 
   
}