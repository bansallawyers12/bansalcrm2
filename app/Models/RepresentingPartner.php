<?php

namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model; 

class RepresentingPartner extends Model
{	use Sortable;
	
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    /** 
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */ 
    	public function partners()
    {
        return $this->belongsTo('App\Models\Partner','partner_id','id');
    }
}
