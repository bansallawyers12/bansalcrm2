<?php
namespace App;

use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class AccountClientReceipt extends Authenticatable
{
    use Notifiable;
	use Sortable;

}
