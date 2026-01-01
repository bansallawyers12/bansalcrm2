<?php
namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use Notifiable;
	use Sortable;
	
	// The authentication guard for admin
    protected $guard = 'admin';
	
	/**
      * The attributes that are mass assignable.
      *
      * @var array
	*/
	protected $fillable = [
        'id', 'role', 'first_name', 'last_name', 'email', 'password', 'decrypt_password', 'country', 'state', 'city', 'address', 'zip', 'profile_img', 'status', 'created_at', 'updated_at'
    ];
    
	/**
      * The attributes that should be hidden for arrays.
      *
      * @var array
	*/
    protected $hidden = [
        'password', 'remember_token',
    ];
	
	public $sortable = ['id', 'first_name', 'last_name', 'email', 'created_at', 'updated_at'];
	
	public function countryData()
    {
        return $this->belongsTo('App\Models\Country','country');
    }
	
	public function stateData()
    {
        return $this->belongsTo('App\Models\State','state');
    }
	public function usertype()
    {
        return $this->belongsTo('App\Models\UserRole', 'role', 'id');
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
     * Get the visaExpiry attribute (maps to visaexpiry column)
     *
     * @param  mixed  $value
     * @return mixed
     */
	public function getVisaExpiryAttribute($value)
    {
        // Map visaExpiry to visaexpiry column
        return $this->attributes['visaexpiry'] ?? null;
    }
	
	/**
     * Set the visaExpiry attribute (maps to visaexpiry column)
     *
     * @param  mixed  $value
     * @return void
     */
	public function setVisaExpiryAttribute($value)
    {
        // Map visaExpiry to visaexpiry column
        // PostgreSQL doesn't accept empty strings for date columns - convert to NULL
        $this->attributes['visaexpiry'] = ($value === '' || $value === null) ? null : $value;
    }
	
	/**
     * Set the agent_id attribute
     * PostgreSQL doesn't accept empty strings for integer columns - convert to NULL
     *
     * @param  mixed  $value
     * @return void
     */
	public function setAgentIdAttribute($value)
    {
        // PostgreSQL doesn't accept empty strings for integer columns - convert to NULL
        if ($value === '' || $value === null) {
            $this->attributes['agent_id'] = null;
        } else {
            $this->attributes['agent_id'] = (int)$value;
        }
    }
	
	/**
     * Set the followers attribute
     * Convert empty strings to NULL for consistency
     *
     * @param  mixed  $value
     * @return void
     */
	public function setFollowersAttribute($value)
    {
        // Convert empty strings to NULL for consistency
        $this->attributes['followers'] = ($value === '') ? null : $value;
    }
	
	/**
     * Set the naati_py attribute
     * Convert empty strings to NULL for consistency
     *
     * @param  mixed  $value
     * @return void
     */
	public function setNaatiPyAttribute($value)
    {
        // Convert empty strings to NULL for consistency
        $this->attributes['naati_py'] = ($value === '') ? null : $value;
    }
	
	/**
     * Set the related_files attribute
     * Convert empty strings to NULL for consistency
     *
     * @param  mixed  $value
     * @return void
     */
	public function setRelatedFilesAttribute($value)
    {
        // Convert empty strings to NULL for consistency
        $this->attributes['related_files'] = ($value === '') ? null : $value;
    }
}

