<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationReminder extends Model
{
    protected $fillable = ['application_id', 'type', 'reminded_at', 'user_id'];

    protected $casts = [
        'reminded_at' => 'datetime',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function user()
    {
        return $this->belongsTo(Admin::class, 'user_id', 'id');
    }
}
