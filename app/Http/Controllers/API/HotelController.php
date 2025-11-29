<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Hotel;
use App\Admin;
use App\Location;
use App\Amenitie;
use DB;
class HotelController extends BaseController
{
	public function __construct(Request $request)
    {	
	}
	
	public function hotelSearch(Request $request){
		$client_id = $request->client_id;
		if($client_id != ''){
			$users = Admin::where('client_id', '=', $client_id)->first();
			if($users){
				$Location = Location::where('name', '=', $request->destination)->first();
				if($Location){
					$query = Hotel::where('user_id',$users->id)->where('destination', $Location->id)->where('status', 1);
					$totalData 	= $query->count();
					$hotels = $query->get();
					$a = array();
					foreach($hotels as $hotel){
						if($hotel->amenities != ''){
							$jamenties = json_decode($hotel->amenities);
							$allameties = '';
							foreach($jamenties as $jam){
								$o = Amenitie::where('id', $jam)->where('user_id', $users->id)->first();
								$allameties .= $o->name.',';
							}
						}
						 
						$a[] = array(
							'name' => $hotel->name,
							'destination' => $Location->name,
							'hotelcategory' => $hotel->hotel_categories,
							'image' => \URL::to('/public/img/hotel_img/').'/'.$hotel->image,
							'description' => $hotel->description,
							'address' => $hotel->address,
							'sale_price' => $hotel->sale_price,
							'offer_price' => $hotel->offer_price,
							'stars' => $hotel->hotel_category,
							'galleryimages' => $hotel->galleryimages,
							'amenties' => rtrim($allameties, ','),
						);
					}
					$success['hotels'] = $a;
					$success['hotelcount'] = $totalData;
					return $this->sendResponse($success, '');
				}else{
					return $this->sendError('Error', array('client_id'=>array('Destination not found')));
				}
			}else{
				return $this->sendError('Error', array('client_id'=>array('Client id not found')));
			}
		}else{
			return $this->sendError('Error', array('client_id'=>array('Client id not found')));
		}
	}
}
?>