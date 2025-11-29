<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;

class AppointmentLog extends Model
{
    use Notifiable;
	use Sortable;
	
    
	
}
