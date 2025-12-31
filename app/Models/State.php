<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
	protected $fillable = [
        'id', 'country_id', 'name', 'created_at', 'updated_at'
    ];
}

