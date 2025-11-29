<?php
namespace App;

use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class City extends Authenticatable
{
    use Notifiable;
	use Sortable;

	protected $fillable = [
        'id', 'name', 'created_at', 'updated_at'
    ];
	
	public $sortable = ['id', 'name', 'created_at', 'updated_at'];
	
	public function user()
    {
        return $this->belongsTo('App\Admin','user_id','id');
    }
}