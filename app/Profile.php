<?php
namespace App;

use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Profile extends Authenticatable
{
    use Notifiable; 
	use Sortable;
	
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	
	 
	protected $fillable = [
        'id', 'company_name', 'address', 'phone', 'other_phone', 'email', 'website', 'abn', 'note', 'logo', 'created_at', 'updated_at'
    ];
   
  public $sortable = ['id', 'created_at', 'updated_at'];
 
}
