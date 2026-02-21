<?php
namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use App\Traits\SanitizesEmail;

class Contact extends Model
{	use Sortable, SanitizesEmail;

    protected $emailAttributes = ['contact_email']; 
	
    /** 
     * The attributes that are mass assignable.
     *
     * @var array
     */

protected $fillable = [
        'id', 'name', 'contact_email', 'contact_phone', 'department', 'branch', 'fax', 'position', 'primary_contact', 'countrycode', 'user_id', 'created_at', 'updated_at'
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
