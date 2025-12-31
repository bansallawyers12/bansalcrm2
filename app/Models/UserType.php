<?php
namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;

class UserType extends Model
{	use Sortable;

	protected $fillable = [
        'id', 'name'
    ];
	
	public $sortable = ['id', 'name'];
}