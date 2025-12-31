<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebsiteSetting extends Model
{
	protected $fillable = [
        'id', 'phone', 'ofc_timing', 'email', 'logo', 'created_at', 'updated_at'
    ];
}