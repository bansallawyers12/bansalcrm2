<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Partner extends Authenticatable
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
	
	//public $sortable = ['partner_name'];
	
	public function workflow()
    {
        return $this->belongsTo('App\Workflow','service_workflow','id');
    }
  
  
    // In App\Models\Partner.php
    public function applications()
    {
        return $this->hasMany(Application::class, 'partner_id');
    }
}
