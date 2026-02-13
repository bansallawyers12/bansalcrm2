<?php
namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use App\Traits\SanitizesEmail;

class Email extends Model
{	use Sortable, SanitizesEmail; 
	
    /** 
     * The attributes that are mass assignable.
     *
     * @var array
     */
		
	public $sortable = ['id', 'created_at', 'updated_at'];

} 
