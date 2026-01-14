<?php
namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{	use Sortable;

	protected $fillable = [
        'id', 'name', 'status', 'created_at', 'updated_at'
    ];
	
	public $sortable = ['id', 'name', 'created_at', 'updated_at'];
	
	public function user()
    {
        return $this->belongsTo('App\Models\User','user_id','id');
    }
	
	public function agentdetail()
    {
        return $this->belongsTo('App\Models\User','agent_id','id');
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
     * Get the preferredIntake attribute (maps to preferredintake column)
     *
     * @param  mixed  $value
     * @return mixed
     */
    public function getPreferredIntakeAttribute($value)
    {
        // Map preferredIntake to preferredintake column
        return $this->attributes['preferredintake'] ?? null;
    }
    
    /**
     * Set the preferredIntake attribute (maps to preferredintake column)
     *
     * @param  mixed  $value
     * @return void
     */
    public function setPreferredIntakeAttribute($value)
    {
        // Map preferredIntake to preferredintake column
        // PostgreSQL doesn't accept empty strings for date columns - convert to NULL
        $this->attributes['preferredintake'] = ($value === '' || $value === null) ? null : $value;
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
   
}