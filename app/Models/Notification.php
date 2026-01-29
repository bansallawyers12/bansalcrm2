<?php

namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{	
    use Sortable;

    protected $fillable = [
        'sender_id', 'receiver_id', 'module_id', 'url',
        'notification_type', 'message', 'receiver_status', 
        'sender_status', 'seen'
    ];

    public $sortable = ['id', 'created_at', 'updated_at'];

    /**
     * Get the checkin log associated with this notification
     */
    public function checkinLog()
    {
        return $this->belongsTo('App\Models\CheckinLog', 'module_id');
    }

    /**
     * Get the sender of this notification
     */
    public function sender()
    {
        return $this->belongsTo('App\Models\Admin', 'sender_id');
    }

    /**
     * Get the receiver of this notification
     */
    public function receiver()
    {
        return $this->belongsTo('App\Models\Admin', 'receiver_id');
    }
}
