<?php

namespace App\Models;

use App\Helpers\PhoneHelper;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;

class ClientPhone extends Model
{	use Sortable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'client_id',
        'user_id',
        'contact_type',
        'client_country_code',
        'client_phone',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    
    /**
     * Append formatted_phone to JSON/array output
     *
     * @var array
     */
    protected $appends = ['formatted_phone'];
    
    /**
     * Mutator: Normalize country code when saving
     * Handles all legacy formats automatically (61, +61, +61 , etc.)
     */
    public function setClientCountryCodeAttribute($value)
    {
        $this->attributes['client_country_code'] = PhoneHelper::normalizeCountryCode($value);
    }
    
    /**
     * Accessor: Always return normalized format when reading
     * This handles legacy data without database migration
     */
    public function getClientCountryCodeAttribute($value)
    {
        return PhoneHelper::normalizeCountryCode($value);
    }
    
    /**
     * Accessor: Get formatted phone number for display
     * Usage: $clientPhone->formatted_phone
     * Returns: "+61 412345678"
     */
    public function getFormattedPhoneAttribute()
    {
        return PhoneHelper::formatPhoneNumber(
            $this->attributes['client_country_code'] ?? '',
            $this->attributes['client_phone'] ?? ''
        );
    }

}
