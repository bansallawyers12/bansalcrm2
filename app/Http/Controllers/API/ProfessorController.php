<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;

use App\Professor;

use Config;

class ProfessorController extends BaseController
{
    /**
     * All Professor list api
     *
     * @return \Illuminate\Http\Response
     */
    public function professorsList(Request $request)
    {
		$requestData = $request->all();
		
		if(!isset($requestData['subject_ids']) || empty(trim($requestData['subject_ids'])))
		{	
			$professorQuery	= Professor::select('id', 'organisation_role', 'which_organisation', 'first_name', 'last_name', 'order_number', 'org_professor_image')->where('id', '!=', '')->where('status', '=', 1);
		}
		else
		{
			$professorQuery	= Professor::select('id', 'organisation_role', 'which_organisation', 'first_name', 'last_name', 'order_number', 'org_professor_image')->where('id', '!=', '')->where('status', '=', 1);
			
			$productIds = @$requestData['subject_ids'];
			$productIds	= explode(',', $productIds);
			
			$professorQuery->whereHas('productData', function ($query) use($productIds){
				$query->select('id');
				$query->whereIn('id', $productIds);
			});
		}		
		
		$data		= $professorQuery->sortable(['order_number'=>'asc'])->get();
	
		$success['professor_list']		=  @$data;
		$success['image_base_path'] 	=  \URL::to('/public/img/profile_imgs').'/';
		
		return $this->sendResponse($success, 'Professors have been fetched suceessfully.');		
	}
}