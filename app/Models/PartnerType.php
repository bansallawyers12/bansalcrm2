<?php
namespace App\Models;

use Kyslik\ColumnSortable\Sortable;

class PartnerType extends BaseModel
{	use Sortable;
	
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

protected $fillable = [
        'id', 'name', 'created_at', 'updated_at'
    ];
  
	public $sortable = ['id', 'created_at', 'updated_at'];
 
	public function categorydata()
    {
        return $this->belongsTo('App\Models\Category','category_id','id'); 
    }
}
