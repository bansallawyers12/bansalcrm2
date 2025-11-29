<?php
namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Template extends Authenticatable
{
    use Notifiable;
use Sortable;
	protected $fillable = [
        'id', 'name', 'created_at', 'updated_at'
    ];
}