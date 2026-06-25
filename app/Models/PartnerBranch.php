<?php

namespace App\Models;

use Kyslik\ColumnSortable\Sortable;

class PartnerBranch extends BaseModel
{
	use Sortable;
	
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	
	protected $fillable = [
        'id', 'partner_id', 'name', 'created_at', 'updated_at'
    ];
	
	public $sortable = ['id', 'name', 'created_at', 'updated_at'];
	
	public function partner()
    {
        return $this->belongsTo('App\Models\Partner', 'partner_id', 'id');
    }
}
