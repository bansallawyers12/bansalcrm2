<?php
namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{	use Sortable;

	protected $fillable = [
        'id', 'status', 'created_at', 'updated_at',
        'is_verified', 'verified_at', 'verified_by',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
    ];
	
	public $sortable = ['id', 'created_at', 'updated_at'];
	
	public function user()
    {
        return $this->belongsTo('App\Models\User','user_id','id');
    }
	
	public function staffuser()
    {
        return $this->belongsTo('App\Models\Admin','assign_to','id');
    }
	public function followupload()
    {
        return $this->belongsTo('App\Models\Followup','id','lead_id');
    }
    public function likes()
    {
        return $this->hasMany('App\Models\Followup','id');
    }

    /**
     * Computed name from first_name + last_name.
     */
    public function getNameAttribute()
    {
        return trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));
    }

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
     * Mutator: Normalize att_country_code when saving
     */
    public function setAttCountryCodeAttribute($value)
    {
        $this->attributes['att_country_code'] = \App\Helpers\PhoneHelper::normalizeCountryCode($value);
    }
    
    /**
     * Accessor: Always return normalized att_country_code when reading
     */
    public function getAttCountryCodeAttribute($value)
    {
        return \App\Helpers\PhoneHelper::normalizeCountryCode($value);
    }
    
    /**
     * Accessor: Get formatted phone number for display
     * Usage: $lead->formatted_phone
     * Returns: "+61 412345678"
     */
    public function getFormattedPhoneAttribute()
    {
        return \App\Helpers\PhoneHelper::formatPhoneNumber(
            $this->attributes['country_code'] ?? '',
            $this->attributes['phone'] ?? ''
        );
    }
    
    /**
     * Accessor: Get formatted attendee phone number for display
     * Usage: $lead->formatted_att_phone
     * Returns: "+61 412345678"
     */
    public function getFormattedAttPhoneAttribute()
    {
        return \App\Helpers\PhoneHelper::formatPhoneNumber(
            $this->attributes['att_country_code'] ?? '',
            $this->attributes['att_phone'] ?? ''
        );
    }

    public function isAustralianNumber()
    {
        $cc = $this->country_code ?? '';
        return $cc === '+61' || str_starts_with((string) $cc, '+61');
    }

    public function isPlaceholderNumber()
    {
        return \App\Helpers\PhoneValidationHelper::isPlaceholderNumber($this->phone ?? '');
    }

    public function needsVerification()
    {
        return $this->isAustralianNumber() && !$this->is_verified && !$this->isPlaceholderNumber();
    }
}