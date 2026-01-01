<?php
namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;

class UserLog extends Model
{	use Sortable;

	protected $fillable = [
        'level', 'user_id', 'ip_address', 'user_agent', 'message', 'created_at', 'updated_at'
    ];
	
	public $sortable = ['id'];
	
}