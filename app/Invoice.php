<?php
namespace App;

use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Invoice extends Authenticatable
{
    use Notifiable;
	use Sortable;
	
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	
	
	protected $fillable = [
        'id', 'customer_id', 'created_at', 'updated_at'
    ];
  
	public $sortable = ['id', 'created_at', 'updated_at'];
 
	public function user()
    {
        return $this->belongsTo('App\Admin','user_id','id');
    }
	
	public function company()
    {
        return $this->belongsTo('App\Admin','user_id','id');
    }
	
	public function staff()
    {
        return $this->belongsTo('App\Admin','seller_id','id');
    }
	
	public function customer()
    {
        return $this->belongsTo('App\Admin','client_id','id');
    }
	public function invoicedetail() 
    {
        return $this->hasMany('App\InvoiceDetail','invoice_id','id');
    }
}
