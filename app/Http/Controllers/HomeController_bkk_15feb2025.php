<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema; 
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Redirect;

use App\WebsiteSetting;
use App\Slider;
use App\Blog;
use App\Contact;
use App\BlogCategory;
use App\OurService;
use App\Testimonial;
use App\WhyChooseus;
use App\HomeContent;
use App\CmsPage;
use App\Mail\CommonMail;

use Illuminate\Support\Facades\Session;
use Config;
use Cookie;

use Mail;
use Swift_SmtpTransport;
use Swift_Mailer;
use Helper;

use Stripe;
use App\Enquiry;


use App\Admin;
use App\ActivitiesLog;
use App\Note;
use Illuminate\Support\Facades\Storage;
use DataTables;
use App\ClientPhone;
use Illuminate\Validation\Rule;
use DateTime;
use Carbon\Carbon;

class HomeController extends Controller
{
	public function __construct(Request $request)
    {	
		$siteData = WebsiteSetting::where('id', '!=', '')->first();
		\View::share('siteData', $siteData);
	}
	
    public function coming_soon()
    {
        return view('coming_soon');
    }
	
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
	
		public static function hextorgb ($hexstring){ 
			$integar = hexdec($hexstring); 
						return array( "red" => 0xFF & ($integar >> 0x10),
			"green" => 0xFF & ($integar >> 0x8),
			"blue" => 0xFF & $integar
			);
		}
	
	public function Page(Request $request, $slug= null)
    { 
		//$client_id = env('TRAVEL_CLIENT_ID', '');
	    //$durl = env('TRAVEL_API_URL', '')."page?slug=".$slug."&client_id=".$client_id;
		
        /*$pagequery 		= CmsPage::where('slug', '=', $slug);		
		$pagedata 	= $pagequery->first();	//for all data
		// dd($pagedata);
		if($pagedata){
		//$pagedata = $this->curlRequest($durl,'GET','');
		 return view('Frontend.cms.index', compact(['pagedata']));
		}else{
			abort(404);
		}*/
      
        if( Blog::where('slug', '=', $slug)->exists() ) {
            $blogdetailquery 		= Blog::where('slug', '=', $slug)->where('status', '=', 1)->with(['categorydetail']);
            $blogdetailists		=  $blogdetailquery->first(); //dd($blogdetailists);
            return view('blogdetail', compact(['blogdetailists']));
        }
        else if(CmsPage::where('slug', '=', $slug)->exists()) {
          //for all data
          $pagequery 	= CmsPage::where('slug', '=', $slug);
          $pagedata 	= $pagequery->first(); //dd($pagedata);
          if($pagedata){
            return view('Frontend.cms.index', compact(['pagedata']));
          }
        }
        else {
          abort(404);
        }
    } 
	
	public function index(Request $request)
    {
		$sliderquery 		= Slider::where('id', '!=', '')->where('status', '=', 1);		
		$sliderData 	= $sliderquery->count();	//for all data
		$sliderlists		=  $sliderquery->orderby('id','ASC')->paginate(5);
	
		$blogquery 		= Blog::where('id', '!=', '')->where('status', '=', 1);		
		$blogData 	= $blogquery->count();	//for all data
		$bloglists		=  $blogquery->orderby('id','DESC')->paginate(3);	
		
		$servicequery 		= OurService::where('id', '!=', '')->where('status', '=', 1);		
		$serviceData 	= $servicequery->count();	//for all data
		$servicelists		=  $servicequery->orderby('id','ASC')->paginate(6);	

		$testimonialquery 		= Testimonial::where('id', '!=', '')->where('status', '=', 1);		
		$testimonialData 	= $testimonialquery->count();	//for all data
		$testimoniallists		=  $testimonialquery->orderby('id','ASC')->paginate(6);	
		
		$whychoosequery 		= WhyChooseus::where('id', '!=', '')->where('status', '=', 1);		
		$whychoosequeryData 	= $whychoosequery->count();	//for all data
		$whychoosequerylists		=  $whychoosequery->orderby('id','ASC')->paginate(6);	
		
		$vals = array(
				'img_path' => public_path().'/captcha/',
				'img_url' => asset('public/captcha'),
				'expiration' => 7200,
				'word_lenght' => 6,
				'font_size' => 15,
				'img_width'	=> '110', 
				'img_height' => '40',
				'colors'	=> array('background' => array(255,175,2),'border' => array(255,175,2),	'text' => array(255,255,255),	'grid' => array(255,255,255))
			);
			$cap = $this->create_captcha($vals); 
			$captcha = $cap['image'];
			session()->put('captchaWord', $cap['word']);
			
	   return view('index', compact(['sliderlists', 'sliderData', 'bloglists', 'blogData', 'servicelists', 'serviceData', 'testimoniallists', 'whychoosequeryData', 'whychoosequerylists', 'testimonialData', 'captcha']));
    }
	
	public function myprofile(Request $request)
    {
		return view('profile');    
    }
	public function contactus(Request $request)
    {
		// dd($request);
		$vals = array(
				'img_path' => public_path().'/captcha/',
				'img_url' => asset('public/captcha'),
				'expiration' => 7200,
				'word_lenght' => 6,
				'font_size' => 15,
				'img_width'	=> '110', 
				'img_height' => '40',
				'colors'	=> array('background' => array(255,175,2),'border' => array(255,175,2),	'text' => array(255,255,255),	'grid' => array(255,255,255))
			);
			$cap = $this->create_captcha($vals); 
			$captcha = $cap['image']; 
			session()->put('captchaWord', $cap['word']);
			
		return view('contact', compact(['captcha']));		
    }
	
	public function refresh_captcha() {
		$vals = array(
			'img_path' => public_path().'/captcha/',
			'img_url' => asset('public/captcha'),
			'expiration' => 7200,
			'word_lenght' => 6,
			'font_size' => 15,
			'img_width'	=> '110',
			'img_height' => '40',
			'colors'	=> array('background' => array(255,175,2),'border' => array(255,175,2),	'text' => array(255,255,255),	'grid' => array(255,255,255))
		);
 
		$cap = $this->create_captcha($vals);
		$captcha = $cap['image'];
		session()->put('captchaWord', $cap['word']);
		echo $cap['image'];
	}
	
	public function contact(Request $request){
	
		$this->validate($request, [
          'fullname' => 'required',
          'email' => 'required',
          'phone' => 'required',
          'subject' => 'required',
          'message' => 'required'
          // 'g-recaptcha-response' => 'required|recaptcha'
        ]);

        $set = \App\Admin::where('id',1)->first();	
		$obj = new Contact;
        $obj->name = $request->fullname;
        $obj->contact_email = $request->email;
        $obj->contact_phone = $request->phone;
        $obj->subject = $request->subject;
        $obj->message = $request->message;
        $saved = $obj->save();
		if($saved){
          $obj1 = new Enquiry;
          $obj1->first_name = $request->fullname;
          $obj1->email = $request->email;
          $obj1->phone = $request->phone;
          $obj1->subject = $request->subject;
          $obj1->message = $request->message;
          $obj1->save();
        }
      
        // dd($set->primary_email);
        // $mailmessage = '<b>Hi Admin,</b><br> You have a New Query<br><b>Name:</b> '.$request->fullname.'<br><b>Email:</b> '.$request->email.'<br><b>Phone:</b> '.$request->phone.'<br><b>Subject:</b> '.$request->subject.'<br><b>Message:</b> '.$request->message;


        // $message = '<html><body>';
        // 	$message .= '<p>Hi Admin,</p>';
        // 	$message .= '<p>You have a New Query<br><b> '.$request->fullname.' </b></p>';
        // 	$message .= '<table><tr>
        // 		<td><b>Email: </b>'.$request->email.'</td></tr>
        // 		<tr><td><b>Name: </b>'.$request->fullname.'</td></tr>
        // 		<tr><td><b>Description: </b>'.$description.'</td></tr>
        // 		<tr><td><b>Phone: </b>'.$request->phone.'</td></tr>
        // 		<tr><td><b>Apoimtment Date/Time: </b>'.$requestData['date'].'/'.$requestData['time'].'</td></tr>
        // 	</table>';
        // $message .= '</body></html>';
      
      	$subject = 'You have a New Query  from  '.$request->fullname;
      	//$this->send_compose_template('info@bansaleducation.au', $subject, 'info@bansaleducation.au', $mailmessage,'Bansal Immigration');
		$details = [
          'title' => 'You have a New Query  from  '.$request->fullname,
          'body' => 'This is for testing email using smtp',
          'subject'=>$subject,
          'fullname' => 'Admin',
          'from' =>$request->fullname,
          'email'=> $request->email,
          'phone' => $request->phone,
          'description' => $request->message
        ];
      
        \Mail::to('Info@bansalimmigration.com.au')->send(new \App\Mail\ContactUsMail($details));
      
         //mail to customer
        $subject_customer = 'You have a new query from Bansal Immigration';
		$details_customer = [
            'title' => 'You have a new query from Bansal Immigration',
            'body' => 'This is for testing email using smtp',
            'subject'=>$subject_customer,
            'fullname' => $request->fullname,
            'from' =>'Admin',
            'email'=> $request->email,
            'phone' => $request->phone,
            'description' => $request->message
        ];
        \Mail::to($request->email)->send(new \App\Mail\ContactUsCustomerMail($details_customer));
      
        return back()->with('success', 'Thanks for sharing your interest. our team will respond to you with in 24 hours.');
	}
	
	public function testimonial(Request $request)
    {
		$testimonialquery 		= Testimonial::where('id', '!=', '')->where('status', '=', 1);		
		$testimonialData 	= $testimonialquery->count();	//for all data
		$testimoniallists		=  $testimonialquery->orderby('id','DESC')->get();
		
	   return view('testimonial', compact(['testimoniallists', 'testimonialData']));
    }

	public function ourservices(Request $request)
    {
		$servicequery 		= OurService::where('id', '!=', '')->where('status', '=', 1);		
		$serviceData 	= 	$servicequery->count();	//for all data 
		$servicelists		=  $servicequery->orderby('id','ASC')->get();	
		
	   return view('ourservices', compact(['servicelists', 'serviceData']));
    }	
	 
	public function blogs(Request $request)
    {
		$blogquery 		= Blog::where('id', '!=', '')->where('status', '=', 1);		
		$blogData 	= $blogquery->count();	//for all data
		$bloglists		=  $blogquery->orderby('id','DESC')->get();	
				
	   return view('blogs', compact(['bloglists', 'blogData']));
    }
	public function blogdetail(Request $request, $slug = null)
    { 
		if(isset($slug) && !empty($slug)){
			if(Blog::where('slug', '=', $slug)->exists()) 
			{ 
			$blogdetailquery 		= Blog::where('slug', '=', $slug)->where('status', '=', 1)->with(['categorydetail']);				
			$blogdetailists		=  $blogdetailquery->first();	
			
			return view('blogdetail', compact(['blogdetailists']));
			} 
			else
			{	
				return Redirect::to('/blogs')->with('error', 'Blog'.Config::get('constants.not_exist'));
			}	
		} 
		else{
			return Redirect::to('/blogs')->with('error', Config::get('constants.unauthorized'));
		}		
    }
	public function servicesdetail(Request $request, $slug = null)
    {
		if(isset($slug) && !empty($slug)){
			if(OurService::where('slug', '=', $slug)->exists()) 
			{
			$servicesdetailquery 		= OurService::where('slug', '=', $slug)->where('status', '=', 1);				
			$servicesdetailists		=  $servicesdetailquery->first();	
			
			return view('servicesdetail', compact(['servicesdetailists']));
			} 
			else
			{	
				return Redirect::to('/ourservices')->with('error', 'Our Services'.Config::get('constants.not_exist'));
			}	
		} 
		else{
			return Redirect::to('/ourservices')->with('error', Config::get('constants.unauthorized'));
		}		
    }

	public function bookappointment()
    {
        return view('bookappointment');
    }
    
    public function bookappointment1()
    {
        return view('bookappointment1');
    }

	
    public function getdatetime(Request $request)
    {   //dd($request->all());
        $enquiry_item = $request->enquiry_item;
        $req_service_id = $request->id;
        //echo $enquiry_item."===".$req_service_id; die;
        if( $enquiry_item != "" && $req_service_id != "")
        {
            if( $req_service_id == 1 ) { //Paid service
                $person_id = 1; //Ajay
                $service_type = $req_service_id; //Paid service
            }
            else if( $req_service_id == 2 ) { //Free service
                if( $enquiry_item == 1 || $enquiry_item == 6 || $enquiry_item == 7 ){
                    //1 => Permanent Residency Appointment
                    //6 => Complex matters: AAT, Protection visa, Federal Cas
                    //7 => Visa Cancellation/ NOICC/ Visa refusals
                    $person_id = 1; //Ajay
                    $service_type = $req_service_id; //Free service
                }
                else if( $enquiry_item == 2 || $enquiry_item == 3 ){
                    //2 => Temporary Residency Appointment
                    //3 => JRP/Skill Assessment
                    $person_id = 2; //Shubam
                    $service_type = $req_service_id; //Free service
                }
                else if( $enquiry_item == 4 ){ //Tourist Visa
                    $person_id = 3; //Tourist
                    $service_type = $req_service_id; //Free service
                }
                else if( $enquiry_item == 5 ){ //Education/Course Change/Student Visa/Student Dependent Visa (for education selection only)
                    $person_id = 4; //Education
                    $service_type = $req_service_id; //Free service
                }
            }
        }
        //echo $person_id."===".$service_type; die;
        $bookservice = \App\BookService::where('id', $req_service_id)->first();//dd($bookservice);
        $service = \App\BookServiceSlotPerPerson::where('person_id', $person_id)->where('service_type', $service_type)->first();//dd($service);
	    if( $service ){
		   $weekendd  =array();
		    if($service->weekend != ''){
				$weekend = explode(',',$service->weekend);
				foreach($weekend as $e){
					if($e == 'Sun'){
						$weekendd[] = 0;
					}else if($e == 'Mon'){
						$weekendd[] = 1;
					}else if($e == 'Tue'){
						$weekendd[] = 2;
					}else if($e == 'Wed'){
						$weekendd[] = 3;
					}else if($e == 'Thu'){
						$weekendd[] = 4;
					}else if($e == 'Fri'){
						$weekendd[] = 5;
					}else if($e == 'Sat'){
						$weekendd[] = 6;
					}
				}
			}
			$start_time = date('H:i',strtotime($service->start_time));
			$end_time = date('H:i',strtotime($service->end_time));

            /*$disabledatesarray = array();  dd($service->disabledates);
			if($service->disabledates != ''){
				$dates = json_decode($service->disabledates,true); dd($dates);
                $dates  = array_flip($dates); //var_dump($dates);
				foreach($dates as $date){
					//$datey = explode('/', $date); //08/03/2024  ["11/03/2024", "13/03/2024"];
					//$disabledatesarray[] = $datey[1].'-'.$datey[0].'-'.$datey[2];
                    $disabledatesarray[] = $date;
				}
                //dd($disabledatesarray);
			}*/
            if($service->disabledates != ''){
                $disabledatesarray =  array();
                if( strpos($service->disabledates, ',') !== false ) {
                    $disabledatesArr = explode(',',$service->disabledates);
                    $disabledatesarray = $disabledatesArr;
                } else {
                    $disabledatesarray = array($service->disabledates);
                }
            } else {
                $disabledatesarray =  array();
            }
            
            // Add the current date to the array
            $disabledatesarray[] = date('d/m/Y'); //dd($disabledatesarray);
            return json_encode(array('success'=>true, 'duration' =>$bookservice->duration,'weeks' => $weekendd,'start_time' =>$start_time,'end_time'=>$end_time,'disabledatesarray'=>$disabledatesarray));
	   }else{
		 return json_encode(array('success'=>false, 'duration' =>0));
	   }
    }
  
  
    public function getdatetimebackend(Request $request)
    {   //dd($request->all());
        $enquiry_item = $request->enquiry_item;
        $req_service_id = $request->id;
        //echo $enquiry_item."===".$req_service_id; die;
        if( $enquiry_item != "" && $req_service_id != "")
        {
            if( $req_service_id == 1 ) { //Paid service
                $person_id = 1; //Ajay
                $service_type = $req_service_id; //Paid service
            }
            else if( $req_service_id == 2 ) { //Free service
                if( $enquiry_item == 1 || $enquiry_item == 6 || $enquiry_item == 7 ){
                    //1 => Permanent Residency Appointment
                    //6 => Complex matters: AAT, Protection visa, Federal Cas
                    //7 => Visa Cancellation/ NOICC/ Visa refusals
                    $person_id = 1; //Ajay
                    $service_type = $req_service_id; //Free service
                }
                else if( $enquiry_item == 2 || $enquiry_item == 3 ){
                    //2 => Temporary Residency Appointment
                    //3 => JRP/Skill Assessment
                    $person_id = 2; //Shubam
                    $service_type = $req_service_id; //Free service
                }
                else if( $enquiry_item == 4 ){ //Tourist Visa
                    $person_id = 3; //Tourist
                    $service_type = $req_service_id; //Free service
                }
                else if( $enquiry_item == 5 ){ //Education/Course Change/Student Visa/Student Dependent Visa (for education selection only)
                    $person_id = 4; //Education
                    $service_type = $req_service_id; //Free service
                }
            }
        }
        //echo $person_id."===".$service_type; die;
        $bookservice = \App\BookService::where('id', $req_service_id)->first();//dd($bookservice);
        $service = \App\BookServiceSlotPerPerson::where('person_id', $person_id)->where('service_type', $service_type)->first();//dd($service);
	    if( $service ){
		   $weekendd  =array();
		    if($service->weekend != ''){
				$weekend = explode(',',$service->weekend);
				foreach($weekend as $e){
					if($e == 'Sun'){
						$weekendd[] = 0;
					}else if($e == 'Mon'){
						$weekendd[] = 1;
					}else if($e == 'Tue'){
						$weekendd[] = 2;
					}else if($e == 'Wed'){
						$weekendd[] = 3;
					}else if($e == 'Thu'){
						$weekendd[] = 4;
					}else if($e == 'Fri'){
						$weekendd[] = 5;
					}else if($e == 'Sat'){
						$weekendd[] = 6;
					}
				}
			}
			$start_time = date('H:i',strtotime($service->start_time));
			$end_time = date('H:i',strtotime($service->end_time));

            /*$disabledatesarray = array();  dd($service->disabledates);
			if($service->disabledates != ''){
				$dates = json_decode($service->disabledates,true); dd($dates);
                $dates  = array_flip($dates); //var_dump($dates);
				foreach($dates as $date){
					//$datey = explode('/', $date); //08/03/2024  ["11/03/2024", "13/03/2024"];
					//$disabledatesarray[] = $datey[1].'-'.$datey[0].'-'.$datey[2];
                    $disabledatesarray[] = $date;
				}
                //dd($disabledatesarray);
			}*/
            if($service->disabledates != ''){
                $disabledatesarray =  array();
                if( strpos($service->disabledates, ',') !== false ) {
                    $disabledatesArr = explode(',',$service->disabledates);
                    $disabledatesarray = $disabledatesArr;
                } else {
                    $disabledatesarray = array($service->disabledates);
                }
            } else {
                $disabledatesarray =  array();
            }
            // Add the current date to the array
            //$disabledatesarray[] = date('d/m/Y'); //dd($disabledatesarray);
            return json_encode(array('success'=>true, 'duration' =>$bookservice->duration,'weeks' => $weekendd,'start_time' =>$start_time,'end_time'=>$end_time,'disabledatesarray'=>$disabledatesarray));
	   }else{
		 return json_encode(array('success'=>false, 'duration' =>0));
	   }
    }

	public function getdisableddatetime(Request $request)
    {
		$requestData = $request->all(); //dd($requestData);
		$date = explode('/', $requestData['sel_date']);
		$datey = $date[2].'-'.$date[1].'-'.$date[0];
        if
        (
            ( isset($request->service_id) && $request->service_id == 1  )
            ||
            (
                ( isset($request->service_id) && $request->service_id == 2 )
                &&
                ( isset($request->enquiry_item) && ( $request->enquiry_item == 1 || $request->enquiry_item == 6 || $request->enquiry_item == 7) )
            )
        ) { //Paid
            if( isset($request->service_id) && $request->service_id == 1  ){ //Ajay Paid Service
                $book_service_slot_per_person_tbl_unique_id = 1;
            } else if( isset($request->service_id) && $request->service_id == 2  ){ //Ajay Free Service
                $book_service_slot_per_person_tbl_unique_id = 2;
            }

            $service = \App\Appointment::select('id', 'date', 'time')
            ->where('status', '!=', 7)
            ->whereDate('date', $datey)
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->whereIn('noe_id', [1, 2, 3, 4, 5, 6, 7, 8])
                    ->where('service_id', 1);
                })
                ->orWhere(function ($q) {
                    $q->whereIn('noe_id', [1, 6, 7])
                    ->where('service_id', 2);
                });
            })->exists();

            $servicelist = \App\Appointment::select('id', 'date', 'time')
            ->where('status', '!=', 7)
            ->whereDate('date', $datey)
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->whereIn('noe_id', [1, 2, 3, 4, 5, 6, 7, 8])
                    ->where('service_id', 1);
                })
                ->orWhere(function ($q) {
                    $q->whereIn('noe_id', [1, 6, 7])
                    ->where('service_id', 2);
                });
            })->get();
        }
        else if( isset($request->service_id) && $request->service_id == 2) { //Free

            if( isset($request->enquiry_item) && ( $request->enquiry_item == 2 || $request->enquiry_item == 3 ) ) { //Temporary and JRP

                if( isset($request->service_id) && $request->service_id == 2  ){ //Shubam Free Service
                    $book_service_slot_per_person_tbl_unique_id = 3;
                }

                $service = \App\Appointment::select('id','date','time')
                ->where('status', '!=', 7)
                ->whereDate('date', $datey)
                ->where(function ($query) {
                    $query->whereIn('noe_id', [2,3])
                    ->Where('service_id', 2);
                })->exists();

                $servicelist = \App\Appointment::select('id','date','time')
                ->where('status', '!=', 7)
                ->whereDate('date', $datey)
                ->where(function ($query) {
                    $query->whereIn('noe_id', [2,3])
                    ->Where('service_id', 2);
                })->get();
            }
            else if( isset($request->enquiry_item) && ( $request->enquiry_item == 4 ) ) { //Tourist Visa

                if( isset($request->service_id) && $request->service_id == 2  ){ //Tourist Free Service
                    $book_service_slot_per_person_tbl_unique_id = 4;
                }

                $service = \App\Appointment::select('id','date','time')
                ->where('status', '!=', 7)
                ->whereDate('date', $datey)
                ->where(function ($query) {
                    $query->whereIn('noe_id', [4])
                    ->Where('service_id', 2);
                })->exists();

                $servicelist = \App\Appointment::select('id','date','time')
                ->where('status', '!=', 7)
                ->whereDate('date', $datey)
                ->where(function ($query) {
                    $query->whereIn('noe_id', [4])
                    ->Where('service_id', 2);
                })->get();
            }

            else if( isset($request->enquiry_item) && ( $request->enquiry_item == 5 ) ) { //Education/Course Change

                if( isset($request->service_id) && $request->service_id == 2  ){ //Education Free Service
                    $book_service_slot_per_person_tbl_unique_id = 5;
                }

                $service = \App\Appointment::select('id','date','time')
                ->where('status', '!=', 7)
                ->whereDate('date', $datey)
                ->where(function ($query) {
                    $query->whereIn('noe_id', [5])
                    ->Where('service_id', 2);
                })->exists();

                $servicelist = \App\Appointment::select('id','date','time')
                ->where('status', '!=', 7)
                ->whereDate('date', $datey)
                ->where(function ($query) {
                    $query->whereIn('noe_id', [5])
                    ->Where('service_id', 2);
                })->get();
            }
        }
        //dd($servicelist);
        $disabledtimeslotes = array();
	    if($service){
            foreach($servicelist as $list){
                $disabledtimeslotes[] = date('g:i A', strtotime($list->time)); //'H:i A'
			}
            //$disabled_slot_arr = \App\BookServiceDisableSlot::select('id','slots')->where('book_service_id', $request->service_id)->whereDate('disabledates', $datey)->get();
            $disabled_slot_arr = \App\BookServiceDisableSlot::select('id','slots')->where('book_service_slot_per_person_id', $book_service_slot_per_person_tbl_unique_id)->whereDate('disabledates', $datey)->get();
            //dd($disabled_slot_arr);
            if(!empty($disabled_slot_arr) && count($disabled_slot_arr) >0 ){
                $newArray = explode(",",$disabled_slot_arr[0]->slots); //dd($newArray);
            } else {
                $newArray = array();
            }
            $disabledtimeslotes = array_merge($disabledtimeslotes, $newArray); //dd($disabledtimeslotes);
		    return json_encode(array('success'=>true, 'disabledtimeslotes' =>$disabledtimeslotes));
	    } else {
            //$disabled_slot_arr = \App\BookServiceDisableSlot::select('id','slots')->where('book_service_id', $request->service_id)->whereDate('disabledates', $datey)->get();
            $disabled_slot_arr = \App\BookServiceDisableSlot::select('id','slots')->where('book_service_slot_per_person_id', $book_service_slot_per_person_tbl_unique_id)->whereDate('disabledates', $datey)->get();
            //dd($disabled_slot_arr);

            if(!empty($disabled_slot_arr) && count($disabled_slot_arr) >0 ){
                $newArray = explode(",",$disabled_slot_arr[0]->slots); //dd($newArray);
            } else {
                $newArray = array();
            }
            $disabledtimeslotes = array_merge($disabledtimeslotes, $newArray); //dd($disabledtimeslotes);
		    return json_encode(array('success'=>true, 'disabledtimeslotes' =>$disabledtimeslotes));
	    }
    }
	
	
	public function stripe($appointmentId)
    {
        $appointmentInfo = \App\Appointment::find($appointmentId);
        if($appointmentInfo){
            $adminInfo = \App\Admin::find($appointmentInfo->client_id);
        } else {
            $adminInfo = array();
        }
        return view('stripe', compact(['appointmentId','appointmentInfo','adminInfo']));
    }

    public function stripePost(Request $request)
    {
        $requestData = $request->all(); //dd($requestData);
        $appointment_id = $requestData['appointment_id'];
        $email = $requestData['customerEmail'];
        $cardName = $requestData['cardName'];
        $stripeToken = $requestData['stripeToken'];
        $currency = "aud";
        $payment_type = "stripe";
        $order_date = date("Y-m-d H:i:s");
        $amount = 150;
        

        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        $customer = Stripe\Customer::create(array("email" => $email,"name" => $cardName,"source" => $stripeToken));

        $payment_result = Stripe\Charge::create ([
            "amount" => $amount * 100,
            "currency" => $currency,
            "customer" => $customer->id,
            "description" => "Paid To bansalimmigration.com.au For Migration Advice By $cardName"
        ]);
        //dd($payment_result);
        //update Order status
        if ( ! empty($payment_result) && $payment_result["status"] == "succeeded")
        { //success
            //Order insertion
            $stripe_payment_intent_id = $payment_result['id'];
            $payment_status = "Paid";
            $order_status = "Completed";
            $appontment_status = 10; //Pending Appointment With Payment Success
            $ins = DB::table('tbl_paid_appointment_payment')->insert([
                'order_hash' => $stripeToken,
                'payer_email' => $email,
                'amount' => $amount,
                'currency' => $currency,
                'payment_type' => $payment_type,
                'order_date' => $order_date,
                'name' => $cardName,
                'stripe_payment_intent_id'=>$stripe_payment_intent_id,
                'payment_status'=>$payment_status,
                'order_status'=>$order_status
            ]);
            if($ins ){
                DB::table('appointments')->where('id',$appointment_id)->update( array('status'=>$appontment_status,'order_hash'=>$stripeToken));
            }
            Session::flash('success', 'Payment successful!');
        } else { //failed
            $stripe_payment_intent_id = $payment_result['id'];
            $payment_status = "Unpaid";
            $order_status = "Payement Failure";
            $appontment_status = 11; //Pending Appointment With Payment Fail
            $ins = DB::table('tbl_paid_appointment_payment')->insert([
                'order_hash' => $stripeToken,
                'payer_email' => $email,
                'amount' => $amount,
                'currency' => $currency,
                'payment_type' => $payment_type,
                'order_date' => $order_date,
                'name' => $cardName,
                'stripe_payment_intent_id'=>$stripe_payment_intent_id,
                'payment_status'=>$payment_status,
                'order_status'=>$order_status
            ]);
            if($ins ){
                DB::table('appointments')->where('id',$appointment_id)->update( array('status'=>$appontment_status,'order_hash'=>$stripeToken));
            }
            //return json_encode(array('success'=>false));
            Session::flash('error', 'Payment failed!');
        }
        return back();
    }
  
  
    public function search_result(Request $request)
    {   //dd($request->all());
        if ( isset($request->search) &&  $request->search != "" ) {
            $search_string 	= $request->search;
        } else {
            $search_string 	= 'search_string';
        }
        /*$query 	= CmsPage::where('title', 'LIKE', '%'.$search_string.'%')
        ->orWhere('slug', 'LIKE', '%' . $search_string . '%')
        ->orWhere('content', 'LIKE', '%' . $search_string . '%')
        ->orWhere('meta_title', 'LIKE', '%' . $search_string . '%')
        ->orWhere('meta_description', 'LIKE', '%' . $search_string . '%')
        ->orWhere('meta_keyward', 'LIKE', '%' . $search_string . '%');*/
      
        $query 	= CmsPage::where('title', 'LIKE', '%'.$search_string.'%')
        ->orWhere('slug', 'LIKE', '%' . $search_string . '%')
        ->orWhere('meta_title', 'LIKE', '%' . $search_string . '%')
        ->orWhere('meta_keyward', 'LIKE', '%' . $search_string . '%');
      
        $totalData 	= $query->count();//dd($totalData);
        //$lists = $query->toSql();
        $lists	= $query->sortable(['id' => 'desc'])->paginate(20); //dd($lists);
        return view('searchresults', compact(['lists', 'totalData']));
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
