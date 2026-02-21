<?php

namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use App\Traits\SanitizesEmail;

class Agent extends Model
{
    use Sortable, SanitizesEmail;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'full_name', 'agent_type', 'related_office', 'struture', 'business_name', 'tax_number', 'contract_expiry_date', 'country_code', 'phone', 'email', 'address', 'city', 'state', 'created_at', 'updated_at', 'country', 'income_sharing', 'claim_revenue'
    ];
    
    /**
     * =========================================
     * PHONE COUNTRY CODE ACCESSORS/MUTATORS
     * =========================================
     */
    
    /**
     * Mutator: Normalize country_code when saving
     */
    public function setCountryCodeAttribute($value)
    {
        $this->attributes['country_code'] = \App\Helpers\PhoneHelper::normalizeCountryCode($value);
    }
    
    /**
     * Accessor: Always return normalized country_code when reading
     */
    public function getCountryCodeAttribute($value)
    {
        return \App\Helpers\PhoneHelper::normalizeCountryCode($value);
    }
    
    /**
     * Accessor: Get formatted phone number for display
     * Usage: $agent->formatted_phone
     * Returns: "+61 412345678"
     */
    public function getFormattedPhoneAttribute()
    {
        return \App\Helpers\PhoneHelper::formatPhoneNumber(
            $this->attributes['country_code'] ?? '',
            $this->attributes['phone'] ?? ''
        );
    }
	
}

