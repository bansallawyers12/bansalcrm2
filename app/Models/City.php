<?php
namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{	use Sortable;

	protected $fillable = [
        'id', 'name', 'created_at', 'updated_at'
    ];
	
	public $sortable = ['id', 'name', 'created_at', 'updated_at'];
	
	public function user()
    {
        return $this->belongsTo('App\Models\Admin','user_id','id');
    }
}

