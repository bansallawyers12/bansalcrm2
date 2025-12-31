<?php
namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{	use Sortable;

	protected $fillable = [
        'id', 'title', 'subject', 'variables', 'alias', 'email_from', 'description', 'created_at', 'updated_at'
    ];
	
	public $sortable = ['id', 'title'];
}