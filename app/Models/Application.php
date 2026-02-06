<?php

namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use Sortable;
	
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	
	protected $fillable = [
        'id', 'client_id', 'user_id', 'product_id', 'partner_id', 'branch', 
        'workflow', 'stage', 'status', 'checklist_sheet_status', 'checklist_sent_at', 'created_at', 'updated_at'
    ];
	
	public $sortable = ['id', 'created_at', 'updated_at'];
    
    public function application_assignee()
    {
        return $this->belongsTo('App\Models\Admin', 'user_id', 'id');
    }
	
	public function product()
	{
		return $this->belongsTo('App\Models\Product', 'product_id', 'id');
	}
	
	public function partner()
	{
		return $this->belongsTo('App\Models\Partner', 'partner_id', 'id');
	}
	
	public function branch()
	{
		return $this->belongsTo('App\Models\PartnerBranch', 'branch', 'id');
	}
	
	public function workflow()
	{
		return $this->belongsTo('App\Models\Workflow', 'workflow', 'id');
	}
	
	public function invoices()
	{
		return $this->hasMany('App\Models\Invoice', 'application_id', 'id');
	}
	
}

