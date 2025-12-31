<?php
namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{	use Sortable; 
	
    /** 
     * The attributes that are mass assignable.
     *
     * @var array
     */
		
	public $sortable = ['id', 'created_at', 'updated_at'];

} 
