<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleItem extends Model
{
	protected $fillable = [
		'id', 'schedule_id', 'fee_amount', 'fee_type', 'commission',
		'created_at', 'updated_at'
    ];
	
	// Relationship
	public function invoiceSchedule()
	{
		return $this->belongsTo(InvoiceSchedule::class, 'schedule_id', 'id');
	}
}