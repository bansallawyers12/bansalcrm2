<?php namespace App\Helpers;
use Auth;
class Settings
{
    static function sitedata($fieldname)
    {
        // Settings table has been removed (only 1 record with invalid date)
        // Return default values instead
        /*
         $siteData = \App\Models\Setting::where('office_id', '=', @Auth::user()->office_id)->first();
         if($siteData){
              return $siteData->$fieldname;
         }else{
             return 'none';
            
         }
        */
        
        // Return default date/time format
        if($fieldname == 'date_format') {
            return 'd/m/Y'; // Default date format
        } elseif($fieldname == 'time_format') {
            return 'g:i A'; // Default time format
        }
        
        return 'none';
	    
    }
    
}
?>