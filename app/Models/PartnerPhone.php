<?php

namespace App\Models;

use App\Helpers\PhoneHelper;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;

class PartnerPhone extends Model
{	use Sortable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

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
     */
    public function setPartnerCountryCodeAttribute($value)
    {
        $this->attributes['partner_country_code'] = PhoneHelper::normalizeCountryCode($value);
    }
    
    /**
     * Accessor: Always return normalized format when reading
     */
    public function getPartnerCountryCodeAttribute($value)
    {
        return PhoneHelper::normalizeCountryCode($value);
    }
    
    /**
     * Accessor: Get formatted phone number for display
     * Usage: $partnerPhone->formatted_phone
     * Returns: "+61 412345678"
     */
    public function getFormattedPhoneAttribute()
    {
        return PhoneHelper::formatPhoneNumber(
            $this->attributes['partner_country_code'] ?? '',
            $this->attributes['partner_phone'] ?? ''
        );
    }

}
