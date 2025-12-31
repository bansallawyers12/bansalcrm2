<?php
namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;

class InvoiceFollowup extends Model
{
	use Sortable;
	
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	
	
	protected $fillable = [
        'id', 'created_at', 'updated_at'
    ];
  
	public $sortable = ['id', 'created_at', 'updated_at'];
	
	public function user()
    {
        return $this->belongsTo('App\Models\Admin','user_id','id');
    }
}
