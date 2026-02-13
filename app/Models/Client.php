<?php

namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use App\Traits\SanitizesEmail;

class Client extends Model
{	use Sortable, SanitizesEmail;
	
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	 
	protected $fillable = [
        'id', 'firstname', 'lastname', 'dob', 'email', 'phone', 'photo', 'address', 'created_at', 'updated_at'
    ];

    /** 
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */ 
    protected $hidden = [
        'password', 'remember_token',
    ];	
	
}

