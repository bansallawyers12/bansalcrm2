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
        'id', 'invoice_id', 'user_id', 'followup_type', 'comment', 'created_at', 'updated_at'
    ];
  
	public $sortable = ['id', 'invoice_id', 'user_id', 'followup_type', 'created_at', 'updated_at'];
	
	public function user()
    {
        return $this->belongsTo('App\Models\Admin','user_id','id');
    }
    
    public function invoice()
    {
        return $this->belongsTo('App\Models\Invoice','invoice_id','id');
    }
}
