<?php
namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use App\Traits\SanitizesEmail;

class Profile extends Model
{
    use Notifiable; 
	use Sortable, SanitizesEmail;
	
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

protected $fillable = [
        'id', 'company_name', 'address', 'phone', 'other_phone', 'email', 'website', 'abn', 'note', 'logo', 'created_at', 'updated_at'
    ];
   
  public $sortable = ['id', 'created_at', 'updated_at'];
 
}
