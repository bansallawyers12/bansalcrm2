<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
//use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Response;
use App\Destination;
use App\Location;
use App\Package;
use App\Admin;
use DB;
use App\Topinclusion;
use App\SuperTopInclusion;
use App\HolidayTheme;
use App\Inclusion;
use App\Exclusion;
use App\Holidaytype;
use App\City;

use Config;

class DestinationController extends BaseController
{
	public function __construct(Request $request)
    {	
		//$siteData = WebsiteSetting::where('id', '!=', '')->first();
		//\View::share('siteData', $siteData);
	}
	
	public function Searchloc(Request $request){
		$skillData = array();
		$searchTerm = $_GET['term']; 
		$Destination = Location::where('name', 'LIKE', '%' . $searchTerm . '%')->get();
			foreach($Destination as $list){
				$data['label'] = $list->name; 
				$data['category'] = 'Destination'; 
				array_push($skillData, $data);
				 
			}
			if(empty($skillData)){
			 $data['label'] = 'Location not found'; 
			$data['category'] = 'Destination';
			array_push($skillData, $data);
		}
		 return json_encode($skillData);
	}
	public function Searchtour(Request $request)
    {
		$skillData = array();
		$client_id = $request->client_id;
		$users = Admin::where('client_id', '=', $client_id)->first();
		if($users){
			$searchTerm = $_GET['term']; 
			DB::enableQueryLog(); 
			$ql = array();
			$query = Destination::where('user_id', '=', $users->id)->where('is_active', '=', 1)->get();
			foreach($query as $l){
				$ql[] = $l->dest_id;
			}
			$Destination = Location::whereIn('id', $ql)->where('name', 'LIKE', '%' . $searchTerm . '%')->get();
			foreach($Destination as $list){
				$data['label'] = $list->name; 
				$data['category'] = 'Destination'; 
				array_push($skillData, $data);
				 
			}
	   
		   /* $Package = Package ::where('package_name', 'LIKE', '%' . $searchTerm . '%')->groupBy('package_name')->get();
		   foreach($Package as $plist){
			  // $data['id'] = $list->dest_name;  
				 $datas['label'] = $plist->package_name; 
				$datas['category'] = 'Tour'; 
				array_push($skillData, $datas);
				 
			} */
	   
	  
		}
		if(empty($skillData)){
			 $data['label'] = 'Location not found'; 
			$data['category'] = 'Destination';
			array_push($skillData, $data);
		}
		 return json_encode($skillData);
    } 
	
	public function Location(Request $request){
		$myloc = Location::where('name', 'LIKE', '%' . $request->name . '%')->first();
		 return $myloc;
	}
	public function destinationList(Request $request){
			$client_id = $request->client_id;
			$users = Admin::where('client_id', '=', $client_id)->first();
			if($users){
				$query = Destination::where('user_id', '=', $users->id)->where('dest_type', '=', $request->dest_type)->where('is_active', '=', '1');
				
				$lists 	= 	$query->with(['mypackage' => function($query)  use ($users)
				{
					$query->select('id','sales_price','destination','user_id','offer_price')->where('user_id', '=', $users->id);
				}, 'desmedia'=>function($query){
						$query->select('id','images');
						
					}, 'myloc'=>function($query){
						$query->select('id','name','slug');
						
					}
				
				])->sortable(['id'=>$request->order])->paginate($request->limit);
				
				
				$success['destination_list'] =  @$lists;
				
				$success['image_base_path'] 	=  \URL::to('/public/img/destination_img').'/';
				$success['image_media_path'] 	=  \URL::to('/public/img/media_gallery').'/';
				return $this->sendResponse($success, '');
			}else{
				return $this->sendError('Error', array('client_id'=>array('Client id not found'))); 
			}
		
	}
	
	public function searchdestinationPackage(Request $request){
		
		$client_id = $request->client_id;
		$users = Admin::where('client_id', '=', $client_id)->first();
		
			if($users){
				if($request->has('tslug') && $request->tslug != ''){
					$myloc = HolidayTheme::where('slug', '=', $request->tslug)->first();
					$myquery = Holidaytype::where('user_id', '=', $users->id)->where('theme_id', '=', $myloc->id)->where('status', '=', '1')->with(['myhteme'])->first();
					$query = Package::where('user_id', '=', $users->id)->where('status', '=', 1);
					$query->whereRaw("find_in_set('".$myloc->id."',package_theme)");
				}else{
					$myloc = Location::where('slug', '=', $request->slug)->first();
					$myquery = Destination::where('user_id', '=', $users->id)->where('dest_id', '=', $myloc->id)->where('is_active', '=', '1')->with(['myloc'])->first();
					$query = Package::where('user_id', '=', $users->id)->where('status', '=', 1);
				$query->where('destination', '=', $myloc->id);
				}
				
				
				//DB::enableQueryLog();  
				
				
				
				 
	
				if ($request->has('price') && $request->price != '') 
				{
					$query->where('price_on_request', '=',  0);
					$pprice = explode('_',$request->price);
					$from 		= 	 trim($pprice[0]);
					$to 		= 	 trim($pprice[1]);
						
					$range = [$from, $to];
					$query->whereRaw("CAST(offer_price AS UNSIGNED) BETWEEN ${from} AND ${to}");
					
				}
				if ($request->has('duration')  && $request->duration != '') 
				{
						
					$durations = explode('_',$request->duration);
					$fromn 		= 	 trim($durations[0]);
					$tod 		= 	 trim($durations[1]);
						
					
					$query->where('no_of_nights', '>=', $fromn);					
						//$query->whereBetween('offer_price',  $range);
					$query->where('no_of_nights', '<=', $tod);
					
				}
				if ($request->has('city')  && $request->city != '') 
				{
					
					$city 		= 	 trim($request->city);
					$query->where('city', '=', $city);					
					
				} 
				if ($request->has('ptype')  && $request->ptype != '') 
				{
					$ptype = json_decode($request->ptype);
					if(!empty($ptype)){
						/* $query->whereHas('packtheme', function ($q) use($ptype){
							$q->whereIn('holiday_type', $ptype);
						}); */	
						
					//	$query->where("package_theme","LIKE",'%'.$ptheme.'%');	
					$query->where(function ($query) use ($ptype) {
						 $query->where('package_theme', 'LIKE', '%'.$ptype[0].'%');
						for($i = 1; $i<count($ptype); $i++){
							
									  $query->orWhere('package_theme', 'LIKE', '%'.$ptype[$i].'%');
							
						}
						});							
					}	
				}	   
				$lists		= $query->with(['media','packloc'])->sortable(['sort_order'=>'ASC'])->get();
				
				$success['packages'] =  @$lists;
				$success['eee'] =  DB::getQueryLog();
				
				$success['image_base_path'] 	=  \URL::to('/public/img/media_gallery').'/';
				$success['image_topinclusion_path'] 	=  \URL::to('/public/img/topinclusion_img').'/';
				return $this->sendResponse($success, '');
			}else{
				return $this->sendError('Error', array('client_id'=>array('Client id not found')));
			}
		
	}
	public function destinationPackage(Request $request){
		$client_id = $request->client_id;
		$users = Admin::where('client_id', '=', $client_id)->first();
			if($users){
				$myloc = Location::where('slug', '=', $request->slug)->first();
				$myquery = Destination::where('user_id', '=', $users->id)->where('dest_id', '=', $myloc->id)->where('is_active', '=', '1')->with(['myloc'])->first();
				
				//DB::enableQueryLog(); 
				
				$query = Package::where('user_id', '=', $users->id)->where('destination', '=', $myloc->id)->where('status', '=', 1);
				
				 
				$Packages		= $query->with(['media'])->sortable(['sort_order'=>'ASC'])->paginate(20);
				$success['destination_detail'] =  @$myquery;
				$success['packages'] =  @$Packages;
				//$success['eee'] =  DB::getQueryLog();
				$success['image_base_path'] 	=  \URL::to('/public/img/media_gallery').'/';
				$success['image_topinclusion_path'] 	=  \URL::to('/public/img/topinclusion_img').'/';
				return $this->sendResponse($success, '');
			}else{
				return $this->sendError('Error', array('client_id'=>array('Client id not found')));
			}
	}
	
	 public function themePackage(Request $request){
		$client_id = $request->client_id;
		$users = Admin::where('client_id', '=', $client_id)->first();
			if($users){
				$myloc = HolidayTheme::where('slug', '=', $request->slug)->first();
				$myquery = Holidaytype::where('user_id', '=', $users->id)->where('theme_id', '=', $myloc->id)->where('status', '=', '1')->with(['myhteme'])->first();
				
				//DB::enableQueryLog(); 
				
				$query = Package::where('user_id', '=', $users->id)->whereRaw("find_in_set('".$myloc->id."',package_theme)")->where('status', '=', 1);
				
				 
				$Packages		= $query->with(['media','packloc'])->sortable(['sort_order'=>'ASC'])->paginate(20);
				//dd(DB::getQueryLog());
				$success['themedetail_detail'] =  @$myquery;
				$success['themedetail'] =  @$myloc;
				$success['packages'] =  @$Packages; 
				//$success['eee'] =  DB::getQueryLog();
				$success['image_base_path'] 	=  \URL::to('/public/img/media_gallery').'/';
				$success['image_topinclusion_path'] 	=  \URL::to('/public/img/topinclusion_img').'/';
				return $this->sendResponse($success, '');
			}else{
				return $this->sendError('Error', array('client_id'=>array('Client id not found')));
			}
	} 
	
	public function topInclusion(Request $request, $inclusionid){
		$client_id = $request->client_id;
		$users = Admin::where('client_id', '=', $client_id)->first();
		if($users){
			$query = SuperTopInclusion::where('id', '=', $inclusionid);
					$Topinclusion		= $query->with(['topinclusion' => function($query)  use ($users){
						$query->select('id','top_inc_id','name','status','image')->where('user_id', '=', $users->id);
					}])->first();
			if($Topinclusion){
				return $this->sendResponse($Topinclusion, '');
			}else{
				return $this->sendError('Error', array('id'=>array('id not found'))); 
			}
		}else{
			return $this->sendError('Error', array('id'=>array('id not found'))); 
		}
	}

	public function Inclusion(Request $request, $inclusionid){
		$Topinclusion = Inclusion::where('id', '=', $inclusionid)->first();
		if($Topinclusion){
			return $this->sendResponse($Topinclusion, '');
		}else{
			return $this->sendError('Error', array('id'=>array('id not found'))); 
		}
	}

	public function Exclusion(Request $request, $inclusionid){
		$TopExclusion = Exclusion::where('id', '=', $inclusionid)->first();
		if($TopExclusion){
			return $this->sendResponse($TopExclusion, '');
		}else{
			return $this->sendError('Error', array('id'=>array('id not found'))); 
		}
	}
	public function FilterData(Request $request){
		$client_id = $request->client_id;
		 
			$users = Admin::where('client_id', '=', $client_id)->first();
			if($users){
				if($request->has('themeslug') && $request->themeslug != ''){
					$myloc = HolidayTheme::where('slug', '=', $request->themeslug)->first();
					$query = Holidaytype::where('user_id', '=', $users->id)->where('theme_id', '=', $myloc->id)->where('status', '=', '1')->with(['myhteme'])->first();
					$Packages = Package::where('user_id', '=', $users->id)->where('price_on_request', '=', 0)->whereRaw("find_in_set('".$myloc->id."',package_theme)")->get();
				}else{
					$myloc = Location::where('slug', '=', $request->slug)->first();
					$query = Destination::where('user_id', '=', $users->id)->where('dest_id', '=', $myloc->id)->where('is_active', '=', '1')->with(['myloc'])->first();
					$Packages = Package::where('user_id', '=', $users->id)->where('price_on_request', '=', 0)->where('destination', '=', $myloc->id)->get();
				}
				
				$ql = array(); 
				$myPackages = Package::where('user_id', '=', $users->id)->get();
				foreach($myPackages as $l){
				$ql[] = $l->id;
				} 
				//$holidaytypes = HolidayTheme::where('id', '!=', '')->get()	;
				
				/*  $holidaytypes 	= 	$mquer->with(['packagetheme' => function($mquer) use($ql) 	{
					$mquer->whereIn('package_id', $ql);
				}])->get();  */
				
				$holidaytypes = \DB::table("holiday_themes")
            ->select("holiday_themes.*",\DB::raw("GROUP_CONCAT(packages.id) as docname"))
            ->leftjoin("packages",\DB::raw("FIND_IN_SET(holiday_themes.id,packages.package_theme)"),">",\DB::raw("'0'"))
            ->where('packages.user_id',$users->id)
            ->groupBy("holiday_themes.id")
            ->get();
				 
				$cities = City::where('user_id', '=', $users->id)->get();
				//DB::enableQueryLog();
				
				
				$pprice = 0;
				$ppricem = 0;
				
				$price  = array();
				$pricem  = array();
				
				$minigt = 0;
				
				
				$maxday = 0;
				$minigta  = array();
				
				
				$maxdaya  = array();
				if(!empty(@$Packages)){
					foreach(@$Packages as $pplist){
							$price[] = $pplist->offer_price;
							$pricem[] = $pplist->offer_price;
							$minigta[] = $pplist->no_of_nights;
							$maxdaya[] = $pplist->no_of_nights;
					}
				} 
				$pprice = @min(@$price);
				$ppricem = @max(@$pricem);
				$minigt = @min(@$minigta);
				$maxday = @max(@$maxdaya);
				$success['min_price'] =  @$pprice;
				$success['max_price'] =  @$ppricem;
				$success['min_nigt'] =  @$minigt;
				$success['max_day'] =  @$maxday;
				$success['holidaytypes'] =  @$holidaytypes;
				$success['cities'] =  @$cities;
				//$success['eee'] =  DB::getQueryLog();
				return $this->sendResponse($success, '');
			}else{
				return $this->sendError('Error', array('client_id'=>array('Client id not found')));
			}
			
	}
}
?>