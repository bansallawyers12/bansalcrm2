<?php
namespace App;

use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class UserType extends Authenticatable
{
    use Notifiable;
	use Sortable;

	protected $fillable = [
        'id', 'name'
    ];
	
	public $sortable = ['id', 'name'];
}