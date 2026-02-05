<?php

namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    use Sortable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'office_name',
        'address',
        'city',
        'state',
        'zip',
        'country',
        'email',
        'phone',
        'mobile',
        'contact_person',
        'choose_admin',
    ];

    /**
     * Get staff members assigned to this office (non-clients).
     */
    public function staff(): HasMany
    {
        return $this->hasMany(Admin::class, 'office_id')
            ->where('role', '!=', 7);
    }

    /**
     * Get clients assigned to this office (role = 7).
     */
    public function clients(): HasMany
    {
        return $this->hasMany(Admin::class, 'office_id')
            ->where('role', 7);
    }

    /**
     * Get active staff for this office.
     */
    public function activeStaff(): HasMany
    {
        return $this->hasMany(Admin::class, 'office_id')
            ->where('role', '!=', 7)
            ->where('status', 1);
    }
}

