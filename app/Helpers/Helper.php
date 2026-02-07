<?php
namespace App\Helpers; // Your helpers namespace 
// NOTE: User model/table has been removed
// use App\Models\User;
use App\Models\Company;
use Auth;

class Helper
{
    public static function changeDateFormate($date,$date_format){
        return \Carbon\Carbon::createFromFormat('Y-m-d', $date)->format($date_format);    
    }
    public static function getUserCompany(): ?object
    {
        $companyId = Auth::user()->comp_id;
        return Company::find($companyId);
    }
}