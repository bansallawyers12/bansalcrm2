<?php
namespace App;

use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Followup extends Authenticatable
{
    use Notifiable;
	use Sortable;

	protected $fillable = [
        'id', 'created_at', 'updated_at'
    ];
	
	public $sortable = ['id', 'created_at', 'updated_at'];
	
	public function user()
    {
        return $this->belongsTo('App\Admin','user_id','id');
    }
	 public function post()
    {
        return $this->belongsTo('App\Lead','lead_id');
    }
	public function followutype()
    {
        return $this->belongsTo('App\FollowupType','followup_type','type');
    }

}