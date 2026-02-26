<?php
namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;

class StaffRole extends Model
{
    use Sortable;

    /**
     * Table name - kept as user_roles for database backward compatibility.
     */
    protected $table = 'user_roles';

    protected $fillable = [
        'id', 'module_access', 'created_at', 'updated_at'
    ];

    public $sortable = ['id', 'name'];
}
