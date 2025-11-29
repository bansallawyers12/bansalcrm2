<?php
namespace App;

use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Category extends Authenticatable
{
    use Notifiable;
	use Sortable;

	protected $fillable = [
        'id', 'created_at', 'updated_at'
    ];
	
	public $sortable = ['id', 'created_at', 'updated_at'];
	
	
}