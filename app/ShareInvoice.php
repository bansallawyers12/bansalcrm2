<?php
namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class ShareInvoice extends Authenticatable
{
    use Notifiable;
	
	protected $fillable = [
		'id', 'created_at', 'updated_at'
    ];
	
	public function company()
    {
        return $this->belongsTo('App\Admin','user_id','id');
    }
}