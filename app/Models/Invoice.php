<?php
namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
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
        return $this->belongsTo('App\Models\Admin','user_id','id');
    }
	
	public function company()
    {
        return $this->belongsTo('App\Models\Admin','user_id','id');
    }
	
	public function staff()
    {
        return $this->belongsTo('App\Models\Admin','seller_id','id');
    }
	
	public function customer()
    {
        return $this->belongsTo('App\Models\Admin','client_id','id');
    }
	public function invoicedetail() 
    {
        return $this->hasMany('App\Models\InvoiceDetail','invoice_id','id');
    }
	
	public function invoiceDetails() 
    {
        return $this->hasMany('App\Models\InvoiceDetail','invoice_id','id');
    }
	
	public function invoicePayments() 
    {
        return $this->hasMany('App\Models\InvoicePayment','invoice_id','id');
    }
}

