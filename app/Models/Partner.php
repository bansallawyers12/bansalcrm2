<?php

namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
	use Sortable;
	
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	
	protected $fillable = [
        'id', 'partner_name', 'service_workflow', 'created_at', 'updated_at'
    ];
	
	public $sortable = ['id', 'partner_name', 'created_at', 'updated_at'];
	
	public function workflow()
    {
        return $this->belongsTo('App\Models\Workflow','service_workflow','id');
    }
  
    public function applications()
    {
        return $this->hasMany(Application::class, 'partner_id');
    }
    
    public function products()
    {
        return $this->hasMany(Product::class, 'partner', 'id');
    }
    
    public function agreements()
    {
        return $this->hasMany(PartnerAgreement::class, 'partner_id');
    }
    
    public function activeAgreements()
    {
        return $this->hasMany(PartnerAgreement::class, 'partner_id')->where('status', 'active');
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
     * Accessor: Get formatted phone number for display
     * Usage: $partner->formatted_phone
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

