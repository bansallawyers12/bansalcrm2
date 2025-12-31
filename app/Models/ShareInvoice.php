<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShareInvoice extends Model
{
	
	protected $fillable = [
		'id', 'created_at', 'updated_at'
    ];
	
	public function company()
    {
        return $this->belongsTo('App\Models\Admin','user_id','id');
    }
}