<?php
namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class ThemeOption extends Authenticatable {
    use Notifiable;

	protected $fillable = [
        'id', 'title', 'alias', 'content', 'created_at', 'updated_at'
    ];
	
	public $sortable = ['id', 'title', 'created_at', 'updated_at'];
}