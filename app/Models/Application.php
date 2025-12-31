<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Application extends Authenticatable
{
    use Notifiable;
    use Sortable;
	
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
    protected $hidden = [
        'password', 'remember_token',
    ];
    
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
	
}

