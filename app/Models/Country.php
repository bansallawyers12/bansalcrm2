<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
	protected $fillable = [
        'id', 'sortname', 'name', 'phonecode', 'created_at', 'updated_at'
    ];
}

