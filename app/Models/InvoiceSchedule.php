<?php
namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;

class InvoiceSchedule extends Model
{
	use Sortable;
	
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	
	protected $fillable = [
        'id', 'user_id', 'client_id', 'application_id', 
        'installment_name', 'installment_date', 'invoice_sc_date',
        'discount', 'installment_no', 'installment_intervel',
        'created_at', 'updated_at'
    ];
  
	public $sortable = ['id', 'created_at', 'updated_at'];
	
	// Relationships
	public function client()
	{
		return $this->belongsTo(Admin::class, 'client_id', 'id');
	}
	
	public function application()
	{
		return $this->belongsTo(Application::class, 'application_id', 'id');
	}
	
	public function scheduleItems()
	{
		return $this->hasMany(ScheduleItem::class, 'schedule_id', 'id');
	}
	
	public function user()
	{
		return $this->belongsTo(Admin::class, 'user_id', 'id');
	}
}
