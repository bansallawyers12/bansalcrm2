<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use App\WebsiteSetting;
use Illuminate\Support\Facades\Session;
use Config;
use App\Admin;
use App\ActivitiesLog;
use App\ClientPhone;

class HomeController extends Controller
{
	public function __construct(Request $request)
    {	
		$siteData = WebsiteSetting::where('id', '!=', '')->first();
		\View::share('siteData', $siteData);
	}
	
	/**************************************************************************
	 * FRONTEND WEBSITE METHODS - REMOVED
	 * Website-only methods (contact, stripe, index, etc.) have been removed
	 * as their routes were commented out and they used non-existent models.
	 * 
	 * ACTIVE METHODS (Client Self-Update Feature):
	 * - emailVerify() - Send email verification link
	 * - emailVerifyToken() - Verify email from link
	 * - thankyou() - Thank you page after verification
	 * - showDobForm() - DOB verification form
	 * - verifyDob() - Validate DOB
	 * - editClient() - Client edit form
	 * - calculateAge() - Helper function
	 * - Page() - Returns 404 for CMS pages (fallback route)
	 **************************************************************************/
	
    /* FRONTEND METHOD - COMMENTED OUT
    public function coming_soon()
    {
        return view('coming_soon');
    }
	*/
	
	/* FRONTEND METHOD - COMMENTED OUT
	public function sicaptcha(Request $request)
    {
		 $code=$request->code;
		
		$im = imagecreatetruecolor(50, 24);
		$bg = imagecolorallocate($im, 37, 37, 37); //background color blue
		$fg = imagecolorallocate($im, 255, 241, 70);//text color white
		imagefill($im, 0, 0, $bg);
		imagestring($im, 5, 5, 5,  $code, $fg);
		header("Cache-Control: no-cache, must-revalidate");
		header('Content-type: image/png');
		imagepng($im);
		imagedestroy($im);
	
    }
	*/
	
	public function Page(Request $request, $slug= null)
    { 
        // CMS Page functionality removed - frontend no longer needed
        return abort(404);
    } 
    //Email Verfiy
    public function emailVerify(Request $request)
    {
        $requestData = $request->all(); //dd($requestData);
        $client_email = $requestData['client_email'];
        $client_id = $requestData['client_id'];
        $client_fname = $requestData['client_fname'];

        //Email To client
        $details = [
            'title' => 'Please click the button below to verify your email address:',
            'body' => 'This is for testing email using smtp',
            'fullname' => $client_fname,
            'email'=> $client_email,
            'client_id'=> $client_id
        ];
        if( \Mail::to($client_email)->send(new \App\Mail\ClientVerifyMail($details))) {
            $message = 'Email is successfully sent at this email';
		    return json_encode(array('success'=>true,'message'=>$message));
        } else {
            $message = 'Email is not sent.Please Try again';
            return json_encode(array('success'=>false,'message'=>$message));
        }
    }

   //Email verfiy link in send email
    public function emailVerifyToken(Request $request,$id = NULL)
    {
        $db_id = $this->decodeString($id);  //dd($db_id);
        if(Admin::where('id', '=', $db_id)->exists()){
            $obj = 	Admin::find($db_id);
			$obj->manual_email_phone_verified = 1;
            $obj->email_verified_at = now();
			$obj->updated_at = now();
            $obj->save();
            return redirect()->route('thankyou')->with('success', 'Client email verified successfully!');
        } else {
            return redirect()->route('thankyou')->with('error', 'Invalid verification link.');
        }
    }

    //Thank you page after email verification
    public function thankyou()
    {
        return view('thankyou');
    }

    //Calculate age for client edit form
    function calculateAge($dob) {
        // Convert the DOB string to a DateTime object
        $birthDate = new \DateTime($dob);
        // Get the current date
        $today = new \DateTime();
        // Calculate the difference between the current date and the birth date
        $diff = $today->diff($birthDate);

        // Get the years and months from the difference
        $ageYears = $diff->y;
        $ageMonths = $diff->m;

        return "$ageYears years and $ageMonths months";
    }


    // Show DOB verification form
    public function showDobForm($encoded_id)
    {
        $client_id = convert_uudecode(base64_decode($encoded_id));
        return view('verify_dob', compact('encoded_id', 'client_id'));
    }

    // Verify DOB and store in session
    public function verifyDob(Request $request)
    {   //dd($request->all());
        $request->validate([
            'dob' => 'required',
            'client_id' => 'required|integer'
        ]);
        //dd($request->dob);
        $dob = '';
        if(isset($request->dob) && $request->dob != ''){
            $dobs = explode('/', $request->dob);
            $dob = $dobs[2].'-'.$dobs[1].'-'. $dobs[0];
        }
        $client = Admin::select('id','dob')->find($request->client_id);
        if ($client && $client->dob == $dob) {
            Session::put('verified_client', $request->client_id);
            return redirect('/editclient/'.base64_encode(convert_uuencode($client->id)));
        } else {
            return back()->withErrors(['dob' => 'Invalid date of birth. Please try again.']);
        }
    }

    //Client edit form link in send email
    public function editclient(Request $request, $encoded_id = NULL)
	{
        if ($request->isMethod('post'))
		{
            $requestData = 	$request->all();
			//echo '<pre>'; print_r($requestData); die;
			$this->validate($request, [
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255'
            ]);
            /*$dob = '';
	        if(array_key_exists("dob",$requestData) && $requestData['dob'] != ''){
	           $dobs = explode('/', $requestData['dob']);
	           $dob = $dobs[2].'-'.$dobs[1].'-'. $dobs[0];
	        }*/
	         $visaExpiry = '';
	        if(array_key_exists("visaExpiry",$requestData) && $requestData['visaExpiry'] != '' ){
	           $visaExpirys = explode('/', $requestData['visaExpiry']);
	          $visaExpiry = $visaExpirys[2].'-'.$visaExpirys[1].'-'. $visaExpirys[0];
	        }
			$obj		= 	Admin::find(@$requestData['id']);
			$first_name = substr(@$requestData['first_name'], 0, 4);
			$obj->first_name	=	@$requestData['first_name'];
			$obj->last_name	=	@$requestData['last_name'];
			$obj->gender	=	@$requestData['gender'];
			$obj->martial_status	=	@$requestData['martial_status'];
            //$obj->email_type	=	@$requestData['email_type'];
			/*$obj->dob	=	@$dob;
            if(isset($dob) && $dob != ""){
                $calculate_age  = $this->calculateAge($dob); //dd($age);
                $obj->age	=	$calculate_age;
            }*/
            //$obj->email	=	@$requestData['email'];
            //$obj->contact_type	=	@$requestData['contact_type'];
            //$obj->country_code	=	@$requestData['country_code'];
			//$obj->phone	=	@$requestData['phone'];
            $obj->address	=	@$requestData['address'];
            if( isset($requestData['address']) && $requestData['address'] != ""){
                //$address = "16-18, Argyle Street, Camden, London, WC1H 8EG, United Kingdom";
                $address = @$requestData['address'];
                $result = app('geocoder')->geocode($address)->get(); //dd($result);
                if(isset($result[0]) && $result[0] != "" ){
                    $coordinates = $result[0]->getCoordinates();
                    $lat = $coordinates->getLatitude();
                    $long = $coordinates->getLongitude();
                } else {
                    $lat = "";
                    $long = "";
                }
                //echo "lat==".$lat; //echo "long==".$long; die();
                $obj->latitude	=	$lat;
                $obj->longitude	=	$long;
            }
            $obj->city	=	@$requestData['city'];
			$obj->state	=	@$requestData['state'];
			$obj->zip	=	@$requestData['zip'];
			$obj->visa_opt = @$requestData['visa_opt'];
			$obj->preferredIntake	=	@$requestData['preferredIntake'];
			$obj->country_passport			=	@$requestData['country_passport'];
			$obj->passport_number			=	@$requestData['passport_number'];
			$obj->visa_type			=		@$requestData['visa_type'];
			$obj->visaExpiry			=	@$visaExpiry;
			$obj->nomi_occupation	=	@$requestData['nomi_occupation'];
			$obj->skill_assessment	=	@$requestData['skill_assessment'];
			$obj->high_quali_aus	=	@$requestData['high_quali_aus'];
			$obj->high_quali_overseas	=	@$requestData['high_quali_overseas'];
			$obj->relevant_work_exp_aus	=	@$requestData['relevant_work_exp_aus'];
			$obj->relevant_work_exp_over	=	@$requestData['relevant_work_exp_over'];
            $obj->married_partner	=	@$requestData['married_partner'];
			$obj->total_points	=	@$requestData['total_points'];
			$obj->start_process	=	@$requestData['start_process'];
			$obj->type	=	@$requestData['type'];
			if(isset($requestData['naati_py']) && !empty($requestData['naati_py'])){
			    $obj->naati_py	=	implode(',',@$requestData['naati_py']);
			} else {
			   	$obj->naati_py	=	'';
			}
            $saved	=	$obj->save();
            if($requestData['client_id'] == ''){
		    	$objs				= 	Admin::find($obj->id);
		    	$objs->client_id	=	strtoupper($first_name).date('ym').$objs->id;
		    	$saveds				=	$objs->save();
			}else{
			    $objs				= 	Admin::find($obj->id);
		    	$objs->client_id	=	$requestData['client_id'];
		    	$saveds				=	$objs->save();
			}
			$route = $request->route;
			if(strpos($request->route,'?')){
				$position = strpos($request->route,'?');
				if ($position !== false) {
					$route = substr($request->route, 0, $position);
				}
			} //dd($route);
			if(!$saved) {
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			} else {
                //save in activity log
                $subject = @$requestData['first_name'].' updated their profile details';
				$objs = new ActivitiesLog;
				$objs->client_id = $request->id;
				$objs->created_by = $request->id;
				$objs->subject = $subject;
				$objs->save();
                return Redirect::to('/editclient/'.base64_encode(convert_uuencode(@$requestData['id'])))->with('success', 'Client updated successfully');
			}
		}
        else
		{
			if(isset($encoded_id) && !empty($encoded_id)) {

                $id = $this->decodeString($encoded_id); //dd($id);
                if (Session::get('verified_client') != $id) {
                    return redirect('/verify-dob/'.$encoded_id)->withErrors(['access' => 'You need to verify your DOB first.']);
                }

                //$client = Client::find($client_id);
                //return view('clients.edit', compact('client'));

                //$id = $this->decodeString($id); //dd($id);
				if(Admin::where('id', '=', $id)->where('role', '=', '7')->exists()) {
					$fetchedData = Admin::find($id); //dd($fetchedData);
                    if(!empty($fetchedData) && $fetchedData->dob != ""){
                        $calculate_age  = $this->calculateAge($fetchedData->dob); //dd($age);
                        $fetchedData->age = $calculate_age;  // Update age in the database
                        $fetchedData->save();
                    }

                    //Check phone record is exist in client phone table
                    if( \App\ClientPhone::where('client_id', $id)->doesntExist() ){
                        if( $fetchedData->phone != "" ) {
                            $oef1 = new \App\ClientPhone;
                            $oef1->user_id = @Auth::user()->id;
                            $oef1->client_id = $id;
                            $oef1->contact_type = $fetchedData->contact_type;
                            $oef1->client_country_code = $fetchedData->country_code;
                            $oef1->client_phone = $fetchedData->phone;
                            $oef1->created_at = date('Y-m-d H:i:s');
                            $oef1->updated_at = date('Y-m-d H:i:s');
                            $oef1->save();
                        }

                        if( $fetchedData->att_phone != "" ) {
                            $oef1 = new \App\ClientPhone;
                            $oef1->user_id = @Auth::user()->id;
                            $oef1->client_id = $id;
                            $oef1->contact_type = '';
                            $oef1->client_country_code = $fetchedData->att_country_code;
                            $oef1->client_phone = $fetchedData->att_phone;
                            $oef1->created_at = date('Y-m-d H:i:s');
                            $oef1->updated_at = date('Y-m-d H:i:s');
                            $oef1->save();
                        }
                    }
                    return view('editclient', compact(['fetchedData']));
				} else {
					return Redirect::to('/editclient')->with('error', 'Client Not Exist');
				}
			} else {
				return Redirect::to('/editclient')->with('error', Config::get('constants.unauthorized'));
			}
		}
    }
	
}
