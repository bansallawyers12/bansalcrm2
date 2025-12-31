<?php

namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{	use Sortable;

public function createddetail()
    {
        return $this->belongsTo('App\Models\Admin','created_by', 'id');
    }	
	
	public function updateddetail()
    {
        return $this->belongsTo('App\Models\Admin','updated_by', 'id');
    }
}
