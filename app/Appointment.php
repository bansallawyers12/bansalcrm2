<?php
namespace App;

use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use Notifiable;
	use Sortable;
	
    /**
     * The attributes that are mass assignable.
     *
     * @var array  
     */
	
	
	protected $fillable = [
        'id','user_id','client_id','timezone','email','noe_id','assinee','full_name','date','time','timeslot_full','title','description','invites','status','related_to','created_at', 'updated_at'
    ];
   
	public $sortable = ['id', 'created_at', 'updated_at'];
	
	public function clients()
    {
        return $this->belongsTo('App\Admin','client_id','id');
    }
	
	public function partners()
    {
        return $this->belongsTo('App\Partner','client_id','id');
    }

    public function user()
    {
        return $this->belongsTo('App\Admin','user_id','id');
    }

    public function assignee_user()
    {
        return $this->belongsTo('App\Admin','assignee','id');
    }

    public function service()
    {
        return $this->belongsTo('App\BookService','service_id','id');
    }

    public function natureOfEnquiry()
    {
        return $this->belongsTo('App\NatureOfEnquiry','noe_id','id');
    }
}
