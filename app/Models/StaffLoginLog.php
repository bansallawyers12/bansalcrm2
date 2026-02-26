<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Kyslik\ColumnSortable\Sortable;

class StaffLoginLog extends Model
{
    use Sortable;

    protected $table = 'staff_login_logs';

    protected $fillable = [
        'user_id', 'ip_address', 'user_agent', 'message', 'created_at', 'updated_at'
    ];

    public $sortable = ['id', 'created_at'];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'user_id');
    }

    public function isLogin(): bool
    {
        return stripos((string) $this->message, 'logged in') !== false;
    }

    public function isLogout(): bool
    {
        return stripos((string) $this->message, 'logged out') !== false;
    }
}
