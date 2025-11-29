<?php
namespace App;

use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class VerifyUser extends Authenticatable
{
    use Notifiable;
	use Sortable;

	protected $fillable = [
        'user_id', 'token'
    ];
	
	public function user()
    {
        return $this->belongsTo('App\Admin', 'user_id');
    }
}