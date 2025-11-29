<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
//use App\Http\Controllers\API\BaseController as BaseController;

use App\Destination;
use App\Package;
use App\Admin;
use App\Topinclusion;
use App\Lead;
use App\Holidaytype;
use App\HolidayTheme;
use App\PackageTheme;
use App\FlightDetail;
use DB;


class PackageController extends BaseController
{
	public function __construct(Request $request)
    {	
		//$siteData = WebsiteSetting::where('id', '!=', '')->first();
		//\View::share('siteData', $siteData);
	}
	
	public function Enquiryhistory(Request $request){
		$client_id = $request->client_id;
		$users = Admin::where('client_id', '=', $client_id)->first();
		if($users){
			$leads = Lead::where('user_id',$users->id)->where('customer_id',$request->user_id)->where('l_type','flight')->orderby('id','DESC')->get();
			$success['leads'] =  @$leads;
			return $this->sendResponse($success, '');
		}else{
			return $this->sendError('Error', array('client_id'=>array('Client id not found'))); 
		}
	}
	public function enquiryContact(Request $request)
    {
		$requestdata 	=  $request->all();
		//print_r($requestdata); die;
		//return $this->sendError('Success', array('message'=>$requestdata)); 
		$client_id = @$requestdata['client_id'];
		$users = Admin::where('client_id', '=', $client_id)->first();
		if($users){
			 $id = $this->decodeString(@$requestdata['flightid']);
			$agent_id = FlightDetail::where('id',$id)->first();
			if(@$requestdata['traveldate'] != ''){
				$traveldate = date('Y-m-d', strtotime(@$requestdata['traveldate']));
			}else{
				$traveldate = date('Y-m-d');
			}
			$leads = new Lead;
			
		$leads->name = @$requestdata['name']; 
		$leads->flight_id = @$id; 
		$leads->agent_id = @$agent_id->agent; 
		$leads->user_id = $users->id;
		$leads->customer_id = @$requestdata['user_id'];
		$leads->package_id = @$requestdata['package_id'];
		$leads->email = @$requestdata['email'];
		$leads->phone = @$requestdata['phone'];
		$leads->city = @$requestdata['city'];
		$leads->going_to = @$requestdata['destination'];
		$leads->location = @$requestdata['location'];
		$leads->from_dest = @$requestdata['from_dest'];
		$leads->service = @$requestdata['subject'];
		$leads->arivaldate = @$requestdata['arivaldate'];
		$leads->priority = 'High';
		$leads->travel_date = $traveldate;
		$leads->adults = @$requestdata['adults'];
		$leads->children = @$requestdata['children'];
		$leads->baby = @$requestdata['infant'];
		$leads->no_of_rooms = @$requestdata['no_room'];
		$leads->check_in_date = date('Y-m-d', strtotime(@$requestdata['check_in_date']));
			$leads->check_out_date =date('Y-m-d', strtotime(@$requestdata['check_out_date']));
		$leads->customize_package = @$requestdata['add_info'];
		$leads->l_type = @$requestdata['type'];
		$leads->ip = $_SERVER['REMOTE_ADDR'];
		$leads->type_of_funtion =  @$requestdata['type_of_funtion'];
		$saved = $leads->save();
		if(!$saved)
			{ 
				return $this->sendError('Error', array('client_id'=>array(Config::get('constants.server_error'))));
			}
			else
			{
				 $dataTransaction = Lead::where('id', '=', $leads->id)->with(['package_detail'=>function($subQuery){
									$subQuery->select('id', 'package_name', 'tour_code');
										
								}])->first(); 
				 $customerInfo = '';
				$customerInfo .= '<tr style="border:1px solid #011224;">';
								$customerInfo .= '<td style="border:1px solid #011224; text-align:center;">'.@$dataTransaction->name.'</td>';
								$customerInfo .= '<td style="border:1px solid #011224; text-align:center;">'.@$dataTransaction->email.'</td>';
								$customerInfo .= '<td style="border:1px solid #011224; text-align:center;">'.@$dataTransaction->phone.'</td>';
								$customerInfo .= '<td style="border:1px solid #011224; text-align:center;">'.@$dataTransaction->city.'</td>';
								$customerInfo .= '<td style="border:1px solid #011224; text-align:center;">'.@$dataTransaction->going_to.'</td>';
								$customerInfo .= '<td style="border:1px solid #011224; text-align:center;">'.@$dataTransaction->travel_date.'</td>';
								$customerInfo .= '<td style="border:1px solid #011224; text-align:center;">'.@$dataTransaction->adults.'</td>';
								$customerInfo .= '<td style="border:1px solid #011224; text-align:center;">'.@$dataTransaction->children.'</td>';
								$customerInfo .= '<td style="border:1px solid #011224; text-align:center;">'.@$dataTransaction->infant.'</td>';
								
								$customerInfo .= '</tr>'; 
								
						if($dataTransaction->l_type == 'eweblink'){
							$mydate = 'Date: '.@$dataTransaction->travel_date;
						}else{
							$mydate = 'Travel Date: '.@$dataTransaction->travel_date;
						}
						$mcity = '';
						if($dataTransaction->city != ''){
							$mcity = 'City: '.$dataTransaction->city;
						}
						
						$mgoing_to = '';
						if($dataTransaction->going_to != ''){
							$mgoing_to = 'Going to: '.$dataTransaction->going_to;
						}
						
						$madults = '';
						if($dataTransaction->adults != ''){
							$madults = 'Adults: '.$dataTransaction->adults;
						}
						
						$mchildren = '';
						if($dataTransaction->children != ''){
							$mchildren = 'Child: '.$dataTransaction->children;
						}
						
						$mbaby = '';
						if($dataTransaction->baby != ''){
							$mbaby = 'Infant: '.$dataTransaction->baby;
						}
						$mservice = '';
						if($dataTransaction->service != ''){
							$mservice = 'Service: '.$dataTransaction->service;
						}
				//email goes to  admin start
					 $replace = array('{company_logo}', '{customer_name}', '{email}', '{phone}', '{travel_date}', '{company_name}', '{company_email}', '{city}', '{goingto}', '{adults}', '{child}', '{infant}', '{service}');					
	$replace_with = array(\URL::to('/').'/public/img/profile_imgs/'.@$users->profile_img, @$dataTransaction->name, @$dataTransaction->email, @$dataTransaction->phone, $mydate, @$users->company_name, @$users->email, $mcity,$mgoing_to,$madults,$mchildren,$mbaby,$mservice); 
				if($users->primary_email !=''){
					$email = $users->primary_email; 
				}else{
					$email = $users->email;
				}
				
		$this->send_email_template($replace, $replace_with, 'new-enquiry-received-for-admin', $email,'New Enquiry recieved', $email,$users);
			
				//email goes to  admin end
				
				//email goes to  user start
					 $replace = array('{logo}', '{name}');					
					$replace_with = array(\URL::to('/').'/public/img/profile_imgs/'.@$users->profile_img, @$dataTransaction->name); 
		
					$this->send_email_template($replace, $replace_with, 'thanks_enquiry', $dataTransaction->email,'Thanks for Enquiry', $email,$users); 
				//email goes to  user end
				$success['data'] = $leads;
				return $this->sendResponse($success, '');
			}
			
		}else{
			return $this->sendError('Error', array('client_id'=>array('Client id not found'))); 
		}
		
    }
	public function sendCall(Request $request)
    {
		$requestdata 	=  $request->all();
		
		$client_id = @$request['client_id'];
		$users = Admin::where('client_id', '=', $client_id)->first();
		if($users){
			if($users->primary_email !=''){
						$email = $users->primary_email;
					}else{
						$email = $users->email;
					}
					$replace = array('{logo}', '{phone}');					
					$replace_with = array('', @$requestdata['phone']);
					$this->send_email_template($replace, $replace_with, 'contactmail', $email,'Call Us enquiry', $email,$users);
				//email goes to  admin end
				
			return $this->sendError('Success', array('message'=>array('Email send successfully'))); 
		}else{
			return $this->sendError('Error', array('client_id'=>array('Client id not found'))); 
		}
	}
	public function sendSubscribe(Request $request)
    {
		$requestdata 	=  $request->all();
		
		$client_id = @$request['client_id'];
		$users = Admin::where('client_id', '=', $client_id)->first();
		if($users){
			if($users->primary_email !=''){
						$email = $users->primary_email;
					}else{
						$email = $users->email;
					}
					$replace = array('{logo}', '{email}');					
					$replace_with = array('', @$requestdata['email']); 
					$this->send_email_template($replace, $replace_with, 'contactmail', $email,'Call Us enquiry', $email,$users);
				//email goes to  admin end
				
			return $this->sendError('Success', array('message'=>array('Email send successfully'))); 
		}else{
			return $this->sendError('Error', array('client_id'=>array('Client id not found'))); 
		}
	}
	public function sendContact(Request $request)
    {
		$requestdata 	=  $request->all();
		
		$client_id = @$request['client_id'];
		$users = Admin::where('client_id', '=', $client_id)->first();
		if($users){
			$leads = new Lead;
		$leads->name = @$requestdata['name'];
		$leads->user_id = $users->id;
		$leads->package_id = @$requestdata['package_id'];
		$leads->email = @$requestdata['email'];
		$leads->phone = @$requestdata['phone'];
		$leads->city = @$requestdata['city'];
		$leads->service = @$requestdata['subject'];
		$leads->travel_date = date('Y-m-d', strtotime(@$requestdata['traveldate']));
		$leads->adults = @$requestdata['adults'];
		$leads->children = @$requestdata['children'];
		$leads->customize_package = @$requestdata['add_info'];
		$leads->ip = $_SERVER['REMOTE_ADDR'];
		$saved = $leads->save();
		if(!$saved)
			{
				return $this->sendError('Error', array('client_id'=>array(Config::get('constants.server_error'))));
			}
			else
			{
				 $dataTransaction = Lead::where('id', '=', $leads->id)->with(['package_detail'=>function($subQuery){
									$subQuery->select('id', 'package_name', 'tour_code');
										
								}])->first(); 
				 $customerInfo = '';
				$customerInfo .= '<tr style="border:1px solid #011224;">';
								$customerInfo .= '<td style="border:1px solid #011224; text-align:center;">'.@$dataTransaction->name.'</td>';
								$customerInfo .= '<td style="border:1px solid #011224; text-align:center;">'.@$dataTransaction->email.'</td>';
								$customerInfo .= '<td style="border:1px solid #011224; text-align:center;">'.@$dataTransaction->phone.'</td>';
								$customerInfo .= '<td style="border:1px solid #011224; text-align:center;">'.@$dataTransaction->city.'</td>';
								$customerInfo .= '<td style="border:1px solid #011224; text-align:center;">'.@$dataTransaction->travel_date.'</td>';
								$customerInfo .= '<td style="border:1px solid #011224; text-align:center;">'.@$dataTransaction->adults.'</td>';
								$customerInfo .= '<td style="border:1px solid #011224; text-align:center;">'.@$dataTransaction->children.'</td>';
								
								$customerInfo .= '</tr>'; 
				//email goes to  admin start
					 $replace = array('{logo}', '{package_name}', '{trip_code}', '{customerInfo}', '{discription}');					
					$replace_with = array('', @$dataTransaction->package_detail->package_name, @$dataTransaction->package_detail->tour_code, $customerInfo, ''); 
					if($users->primary_email !=''){
						$email = $users->primary_email;
					}else{
						$email = $users->email;
					}
					$this->send_email_template($replace, $replace_with, 'new-enquiry-received-for-admin', $email,@$requestdata['subject'], $email,$users);
				//email goes to  admin end
				
				//email goes to  admin start
					 $replace = array('{logo}', '{name}');					
					$replace_with = array('', @$dataTransaction->name); 
		
					$this->send_email_template($replace, $replace_with, 'thanks_enquiry', $dataTransaction->email,$users);
				//email goes to  admin end
				$success['data'] = $leads;
				return $this->sendResponse($success, '');
			}
			
		}else{
			return $this->sendError('Error', array('client_id'=>array('Client id not found'))); 
		}
		
    }
	
	public function sendCareer(Request $request)
    {
		$requestdata 	=  $request->all();
		
		$client_id = @$request['client_id'];
		$users = Admin::where('client_id', '=', $client_id)->first();
		if($users){
			$customerInfo = '';
				$customerInfo .= '<tr style="border:1px solid #011224;">';
								$customerInfo .= '<td style="border:1px solid #011224; text-align:center;">'.@$requestdata['name'].'</td>';
								$customerInfo .= '<td style="border:1px solid #011224; text-align:center;">'.@$requestdata['email'].'</td>';
								$customerInfo .= '<td style="border:1px solid #011224; text-align:center;">'.@$requestdata['phone'].'</td>';
								$customerInfo .= '<td style="border:1px solid #011224; text-align:center;">'.@$requestdata['message'].'</td>';
								
								
								$customerInfo .= '</tr>'; 
				//email goes to  admin start
					 $replace = array('{logo}', '{package_name}', '{trip_code}', '{customerInfo}', '{discription}');					
					$replace_with = array('', '', '', $customerInfo, ''); 
					if($users->primary_email !=''){
						$email = $users->primary_email;
					}else{
						$email = $users->email;
					}
					$this->send_email_template($replace, $replace_with, 'new-enquiry-received-for-admin', $email,@$requestdata['subject'], $email,$users);
				//email goes to  admin end
				
				//email goes to  admin start
					 $replace = array('{logo}', '{name}');					
					$replace_with = array('', @$dataTransaction->name); 
		
					$this->send_email_template($replace, $replace_with, 'thanks_enquiry', $dataTransaction->email,$users);
				//email goes to  admin end
				$success['data'] = $leads;
				return $this->sendResponse($success, '');
			
			
		}else{
			return $this->sendError('Error', array('client_id'=>array('Client id not found'))); 
		}
		
    }
	
	public function PopularTour(Request $request){
		$client_id = $request->client_id;
		$users = Admin::where('client_id', '=', $client_id)->first();
		if($users){
			$Packages = Package::where('user_id', '=', $users->id)->where('is_popular', '=', 1)->with(['media','packloc'])->paginate(12);		
			$success['popular_pack'] =  @$Packages;
			$success['image_gallery_path'] 	=  \URL::to('/public/img/media_gallery').'/';
			return $this->sendResponse($success, '');
		}else{
			return $this->sendError('Error', array('client_id'=>array('Client id not found'))); 
		}
	}
	public function holidaytype(Request $request){
		$client_id = $request->client_id;
		$users = Admin::where('client_id', '=', $client_id)->first();
		if($users){
			$query = HolidayTheme::where('id', '!=', '');
			$userid = $users->id;
			$Packages = $query->with(['holidaytype' => function($query)  use ($userid){
				$query->select('id','theme_id','name','status','image')->where('user_id', '=', $userid);
			}])->orderby('name','ASC')->get();	
			$success['holidaytype_pack'] =  @$Packages;
			$success['image_gallery_path'] 	=  \URL::to('/public/img/themes_img').'/';
			return $this->sendResponse($success, '');
		}else{
			return $this->sendError('Error', array('client_id'=>array('Client id not found'))); 
		}
	}
	public function holidaytypePackage(Request $request,$typeid= null){
		$client_id = $request->client_id;
		$users = Admin::where('client_id', '=', $client_id)->first();
		if($users){
			$ql = array();
			$Packagestheme = PackageTheme::where('holiday_type', '=', $typeid)->get();
			foreach($Packagestheme as $l){
				$ql[] = $l->package_id;
			}
			DB::enableQueryLog(); 
			$query = Package::whereIn('id', $ql)->where('user_id', '=', $users->id)->with(['media','packloc']);
			$totalcount = $query->count();
			$Packages = $query->paginate(6);
			$success['package_theme'] =  @$Packages;
			$success['totalcount'] =  @$totalcount;
			$success['package_s'] =  DB::getQueryLog();
			$success['image_gallery_path'] 	=  \URL::to('/public/img/media_gallery').'/';
			return $this->sendResponse($success, '');
		}else{
			return $this->sendError('Error', array('client_id'=>array('Client id not found'))); 
		}
	}
	public function Packagedetail(Request $request){
		$client_id = $request->client_id;
		$users = Admin::where('client_id', '=', $client_id)->first();
	
			if($users){
			//	DB::enableQueryLog(); 
				$query = Package::where('user_id', '=', $users->id)->where('slug', '=', $request->slug)->where('status', '=', '1');
				
				 $lists 	= 	$query->with(['packigalleries' => function($query)
					{
						$query->select('id','package_id','package_gallery_image_alt','package_gallery_image');
						$query->with(['galleriesmedia' => function($subQuery){
							$subQuery->select('id','images');
						}]);
					}, 'packhotel'=>function($query){
							$query->select('id', 'package_id', 'hotel_name');
							
						   $query->with(['hotel' => function($subsQuery){
							$subsQuery->select('id','name','hotel_category','image_alt','image','description', 'address');
						}]);  
					}, 'packitinerary'=>function($query){
						$query->select('id', 'package_id', 'title', 'details', 'itinerary_image', 'foodtype');
						
					}, 'bamedia'=>function($query){
						$query->select('id','images');
						
					}])->first();
				$Packages = Package::where('user_id', '=', $users->id)->where('destination', '=', $lists->destination)->with(['media'])->paginate(3);		
				$success['package_detail'] =  @$lists;
				$success['related_pack'] =  @$Packages;
				//$success['package_s'] =  DB::getQueryLog();
				$success['image_gallery_path'] 	=  \URL::to('/public/img/media_gallery').'/';
				$success['pdfs'] 	=  \URL::to('/public/img/pdfs').'/';
				$success['image_topinclusion_path'] 	=  \URL::to('/public/img/topinclusion_img').'/';
				$success['image_hotel_path'] 	=  \URL::to('/public/img/hotel_img').'/';
				return $this->sendResponse($success, '');
			}else{
				return $this->sendError('Error', array('client_id'=>array('Client id not found'))); 
			}
		
	}	
}
?>	