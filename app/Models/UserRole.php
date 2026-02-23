<?php
namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{	use Sortable;

	protected $fillable = [
        'id', 'module_access', 'created_at', 'updated_at'
    ];
	
	public $sortable = ['id', 'name'];
}