<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Admin;
use App\Offer;
 
use Auth;
use Config;
 
class OfferController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
	/**
     * All Vendors.
     *
     * @return \Illuminate\Http\Response
     */
	public function index(Request $request)
	{
		$query 		= Offer::where('user_id', '=', Auth::user()->id);
		
		$totalData 	= $query->count();	//for all data

		$lists		=  $query->get();
		
		return view('Admin.offer.index',compact(['lists', 'totalData']));  
	}
	
	public function create(Request $request)
	{
		
		return view('Admin.offer.create');
	}
	
	public function store(Request $request)
	{
		if ($request->isMethod('post')) 
		{
			$this->validate($request, [
										'name' => 'required|max:255'
									  ]);
			
			$requestData 		= 	$request->all();
			
			$obj				= 	new Offer;
			$obj->user_id	=	Auth::user()->id;
			$obj->name	=	@$requestData['name'];
			$obj->subtitle	=	@$requestData['subtitle'];
			$obj->expiry_date	=	@$requestData['expire_date'];
			$obj->description	=	@$requestData['description'];
			$obj->type	=	@$requestData['type'];
			
			// Gallery Image Upload Function Start 						  
					if($request->hasfile('image')) 
					{	
						$gallery_image = $this->uploadFile($request->file('image'), Config::get('constants.gallery_img')); 
					}
					else
					{
						$gallery_image = NULL;
					}	 	
				// Gallery Image Upload Function End
				
			$obj->image			=	@$gallery_image;
			$saved		 		=	$obj->save();  
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return Redirect::to('/admin/offer/index')->with('success', 'Offer Added Successfully');
			}				
		}	
		
	}
	
	public function edit(Request $request, $id = NULL)
	{			
		//check authorization end
	
		if ($request->isMethod('post')) 
		{
			$this->validate($request, [
										'name' => 'required|max:255'
									  ]);
			
			$requestData 		= 	$request->all();
			
			$obj				=  Offer::find($requestData['id']);
			$obj->user_id	=	Auth::user()->id;
			$obj->name	=	@$requestData['name'];
			$obj->subtitle	=	@$requestData['subtitle'];
			$obj->expiry_date	=	@$requestData['expire_date'];
			$obj->description	=	@$requestData['description'];
			$obj->type	=	@$requestData['type'];
			
			/* Gallery Image Upload Function Start */						  
			if($request->hasfile('image')) 
			{	
				/* Unlink File Function Start */ 
					if($requestData['image'] != '')
						{
							$this->unlinkFile($requestData['old_image'], Config::get('constants.gallery_img'));
						}
				/* Unlink File Function End */
				
				$gallery_image = $this->uploadFile($request->file('image'), Config::get('constants.gallery_img'));
			} 
			else
			{
				$gallery_image = @$requestData['old_image'];
			}		
		/* Gallery Image Upload Function End */ 
		
			$obj->image			=	@$gallery_image;
						
			$saved				=	$obj->save();
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return Redirect::to('/admin/offer/index')->with('success', 'Gallery Edited Successfully');
			}				
		}
		else
		{	
			if(isset($id) && !empty($id))
			{
				$id = $this->decodeString($id);	
				if(Offer::where('id', '=', $id)->exists()) 
				{
					$fetchedData = Offer::find($id);
					return view('Admin.offer.edit', compact(['fetchedData']));
				}
				else
				{
					return Redirect::to('/admin/offer/index')->with('error', 'Offer Not Exist');
				}	
			}
			else
			{
				return Redirect::to('/admin/offer/index')->with('error', Config::get('constants.unauthorized'));
			}		 
		}				
	}
	 
	
}
