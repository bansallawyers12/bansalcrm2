<?php
namespace App;

use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class AdminMeta extends Authenticatable
{
    use Notifiable;
	use Sortable;
	
	// The authentication guard for admin
    protected $guard = 'admin';
	
	/**
      * The attributes that are mass assignable.
      *
      * @var array
	*/
	protected $fillable = [
        'id', 'created_at', 'updated_at'
    ];
    
	/**
      * The attributes that should be hidden for arrays.
      *
      * @var array
	*/
    protected $hidden = [
        'password', 'remember_token',
    ];
	
	public $sortable = ['id', 'first_name', 'last_name', 'email', 'created_at', 'updated_at'];
	
}