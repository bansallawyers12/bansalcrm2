<?php

namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;

class ApplicationActivitiesLog extends Model
{
    use Sortable;

    protected $table = 'application_activities_logs';

    protected $fillable = [
        'app_id', 'stage', 'type', 'comment', 'title', 'description', 'user_id',
    ];
}
