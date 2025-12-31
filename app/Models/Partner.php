<?php

namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
	use Sortable;
	
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	
	protected $fillable = [
        'id', 'partner_name', 'service_workflow', 'created_at', 'updated_at'
    ];
	
	public $sortable = ['id', 'partner_name', 'created_at', 'updated_at'];
	
	public function workflow()
    {
        return $this->belongsTo('App\Models\Workflow','service_workflow','id');
    }
  
    public function applications()
    {
        return $this->hasMany(Application::class, 'partner_id');
    }
}

