<?php
namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;

class IncomeSharing extends Model
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

public function branch()
    {
        return $this->belongsTo('App\Models\Branch','rec_id','id');
    }
	
	public function invoice()
    {
        return $this->belongsTo('App\Models\Invoice','invoice_id','id');
    }
}
