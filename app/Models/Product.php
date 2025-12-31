<?php

namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
	use Sortable;
	
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	 
	protected $fillable = [
        'id', 'name', 'partner', 'branches', 'product_type', 'revenue_type', 'duration', 'intakemonth', 'descripton', 'note', 'created_at', 'updated_at'
    ];
	
	public $sortable = ['id', 'name', 'created_at', 'updated_at'];
	
	public function branchdetail()
    {
        return $this->belongsTo('App\Models\PartnerBranch','branches','id');
    }
	
	public function partnerdetail()
    {
        return $this->belongsTo('App\Models\Partner','partner','id');
    }
}
