<?php
namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;

class LeadService extends Model
{	use Sortable;  
	
    /**
     * The attributes that are mass assignable.
     *
     * @var array  
     */

protected $fillable = [
        'id', 'created_at', 'updated_at'
    ];
   
	public $sortable = ['id', 'created_at', 'updated_at'];
	
}
