<?php
namespace App\Helpers; // Your helpers namespace 
// NOTE: User model/table has been removed
// use App\Models\User;
use App\Models\Company;
use App\Models\Profile;
use Auth;

class Helper
{
    public static function changeDateFormate($date,$date_format){
        return \Carbon\Carbon::createFromFormat('Y-m-d', $date)->format($date_format);    
    }
    public static function getUserCompany(): ?object
    {
        $companyId = Auth::user()->comp_id ?? null;
        return $companyId ? Company::find($companyId) : null;
    }

    /**
     * Get the default CRM profile (Bansal Education Group - Profile ID 1).
     * Used for all non-invoice contexts: emails, receipts, templates, etc.
     *
     * @return \App\Models\Profile|null
     */
    public static function defaultCrmProfile(): ?Profile
    {
        $profileId = config('app.default_profile_id', 1);
        return Profile::find($profileId);
    }

    /**
     * Get the default CRM company name.
     *
     * @return string
     */
    public static function defaultCrmCompanyName(): string
    {
        $profile = self::defaultCrmProfile();
        return $profile ? $profile->company_name : 'Bansal Education Group';
    }
}