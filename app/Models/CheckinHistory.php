<?php

namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;

class CheckinHistory extends Model
{
    use Sortable;

    protected $fillable = [
        'id', 'subject', 'created_by', 'checkin_id',
        'description', 'created_at', 'updated_at'
    ];

    public $sortable = ['id', 'created_at', 'updated_at'];

    /**
     * Get the checkin log for this history entry
     */
    public function checkinLog()
    {
        return $this->belongsTo('App\Models\CheckinLog', 'checkin_id');
    }

    /**
     * Get the user who created this history entry
     */
    public function creator()
    {
        return $this->belongsTo('App\Models\Admin', 'created_by');
    }
}
