<?php
namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{	use Sortable; 
	
    /** 
     * The attributes that are mass assignable.
     *
     * @var array
     */

protected $fillable = [
        'id', 'srname', 'first_name', 'middle_name', 'last_name', 'company_name', 'contact_display_name', 'contact_email', 'contact_phone', 'work_phone', 'website', 'designation', 'department', 'skype_name', 'facebook_name', 'twitter_name', 'linkedin_name', 'instagram_name', 'youtube_name', 'country', 'address', 'city', 'zipcode', 'phone', 'created_at', 'updated_at'
    ]; 
  
	public $sortable = ['id', 'created_at', 'updated_at'];
 
	public function company()
    {
        return $this->belongsTo('App\Models\Admin','user_id','id');
    }
    
    /**
     * =========================================
     * PHONE COUNTRY CODE ACCESSORS/MUTATORS
     * =========================================
     */
    
    /**
     * Mutator: Normalize countrycode when saving
     */
    public function setCountrycodeAttribute($value)
    {
        $this->attributes['countrycode'] = \App\Helpers\PhoneHelper::normalizeCountryCode($value);
    }
    
    /**
     * Accessor: Always return normalized countrycode when reading
     */
    public function getCountrycodeAttribute($value)
    {
        return \App\Helpers\PhoneHelper::normalizeCountryCode($value);
    }
    
    /**
     * Accessor: Get formatted phone number for display
     * Usage: $contact->formatted_phone
     * Returns: "+61 412345678"
     */
    public function getFormattedPhoneAttribute()
    {
        return \App\Helpers\PhoneHelper::formatPhoneNumber(
            $this->attributes['countrycode'] ?? '',
            $this->attributes['contact_phone'] ?? ''
        );
    }
	
	/*public function desmedia() 
    {
        return $this->belongsTo('App\Models\MediaImage','dest_image','id');
    }
	
	public function mypackage() 
    {
        return $this->hasMany('App\Models\Package','destination','id');
    } */
} 
