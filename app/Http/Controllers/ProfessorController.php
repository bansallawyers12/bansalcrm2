<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Professor;
use App\Product;
use App\WebsiteSetting;
use App\SeoPage;


use Config;
use Cookie;

class ProfessorController extends Controller
{
	public function __construct(Request $request)
    {
		//remove all cokies start
			\Cookie::queue(\Cookie::forget('product_id'));
			\Cookie::queue(\Cookie::forget('product_other_info_id'));
			\Cookie::queue(\Cookie::forget('quantity'));
			\Cookie::queue(\Cookie::forget('subject_ids'));
		//remove all cokies end	
		
		$siteData = WebsiteSetting::where('id', '!=', '')->first();
		\View::share('siteData', $siteData);
	}

	/**
     * All Professors.
     *
     * @return \Illuminate\Http\Response
     */
	public function index(Request $request)
	{	
		//all data start
			$professorQuery	= Professor::select('id', 'organisation_role', 'which_organisation', 'first_name', 'last_name', 'order_number', 'org_professor_image')->where('id', '!=', '')->where('status', '=', 1)->with(['organisationData'=>function($subQuery){
				$subQuery->select('id', 'profile_img');
			}]);
			$professors = $professorQuery->get();	
			
			$subjects	= Product::select('id', 'subject_name')->where('id', '!=', '')->where('status', '=', 1)->get();
		//all data end
		
		$totalData 	= $professorQuery->count();	//for all data
		
		if ($request->has('search_term')) 
		{
			$search_term 		= 	$request->input('search_term');
			if(trim($search_term) != '')
			{	
				$searchTermArray = explode(',', $search_term);
				
				$professorIds = array();
				$productIds = array();
			
				foreach($searchTermArray as $search)
				{
					$breakDownArray	= explode('_', $search);	
					$value = $this->decodeString($breakDownArray[1]);
					
					if($breakDownArray[0] == 'pro')
					{
						array_push($professorIds, $value);
					}
					else
					{
						array_push($productIds, $value);
					}			
				}
				if(!empty($productIds))
				{
					$professorQuery->whereHas('productData', function ($query) use($productIds){
						$query->select('id');
						$query->whereIn('id', $productIds);
					});
				}
				
				if(!empty($professorIds))
				{
					if(!empty($productIds))
						{	
							$professorQuery->orwhereIn('id', $professorIds);
						}
					else
						{
							$professorQuery->whereIn('id', $professorIds);
						}			
				}
			}
			
			$totalData 	= $professorQuery->count();
		}

		$lists		= $professorQuery->sortable(['order_number'=>'asc'])->paginate(config('constants.unlimited'));
	
		$seoDetails = SeoPage::where('page_slug', '=', 'our_faculties')->first();
		return view('Frontend.professors.index',compact(['professors', 'subjects', 'lists', 'totalData', 'seoDetails']));	
	}
}