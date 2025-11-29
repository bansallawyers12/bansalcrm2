<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Admin;
use App\Education;

use Auth;
use Config;

class EducationController extends Controller
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
	
	
	
	
	public function store(Request $request)
	{		
		//check authorization end
		if ($request->isMethod('post'))  
		{
			
			
			$requestData 		= 	$request->all();
			 $course_start = date('Y-m-d', strtotime($requestData['course_start']));
			 $course_end = date('Y-m-d', strtotime($requestData['course_end']));
			$obj				= 	new Education; 
			$obj->degree_title			=	@$requestData['degree_title'];
			$obj->degree_level		=	@$requestData['degree_level'];
			$obj->institution		=	@$requestData['institution'];
			$obj->course_start		=	@$course_start;
			$obj->course_end		=	@$course_end; 
			$obj->subject_area		=	@$requestData['subject_area']; 
			$obj->subject			=	@$requestData['subject'];
			$obj->ac_score	=	@$requestData['academic_score_type'];
			
			$obj->score	=	@$requestData['academic_score'];
			$obj->user_id	=	Auth::user()->id;
			$obj->client_id	=	@$requestData['client_id'];
			$saved				=	$obj->save();  
			
			
			if(!$saved)
			{
				$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
			}
			else
			{
				$response['status'] 	= 	true;
				$response['message']	=	'Education Created Successfully';
			}	
			echo json_encode($response);		
		}	

		//return view('Admin.tasks.index');	 
	}
	
	public function edit(Request $request, $id = NULL)
	{
	
		//check authorization end
		
		if ($request->isMethod('post')) 
		{
			$requestData 		= 	$request->all();
			 $course_start = date('Y-m-d', strtotime($requestData['course_start']));
			 $course_end = date('Y-m-d', strtotime($requestData['course_end']));
			$obj					= 	 Education::find($requestData['id']); 
			$obj->degree_title		=	@$requestData['degree_title'];
			$obj->degree_level		=	@$requestData['degree_level'];
			$obj->institution		=	@$requestData['institution'];
			$obj->course_start		=	@$course_start;
			$obj->course_end		=	@$course_end; 
			$obj->subject_area		=	@$requestData['subject_area']; 
			$obj->subject			=	@$requestData['subject'];
			$obj->ac_score	=	@$requestData['academic_score_type'];
			
			$obj->score	=	@$requestData['academic_score'];
			
			$saved				=	$obj->save();  
			
			
			if(!$saved)
			{
				$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
			}
			else
			{
				$response['status'] 	= 	true;
				$response['message']	=	'Education Created Successfully';
			}	
			echo json_encode($response);					
		}

			
		
	}
	
	
	public function geteducations(Request $request){
		$client_id = $request->clientid;
		
		$edulists = \App\Education::where('client_id', $client_id)->orderby('created_at','DESC')->get();
		ob_start();
		foreach($edulists as $edulist){
			$subjectdetail = \App\Subject::where('id',$edulist->subject)->first();
			$subjectareadetail = \App\SubjectArea::where('id',$edulist->subject_area)->first();
			?>
			<div class="education_item" id="edu_id_<?php echo $edulist->id; ?>">
				<div class="row">
					<div class="col-md-5">
						<div class="title_desc">
							<h6><?php echo @$edulist->degree_title; ?></h6>
							<p><?php echo @$edulist->institution; ?></p>
						</div>
					</div>
					<div class="col-md-7">
						<div class="education_info">
							<div class="edu_date"><?php echo date('M Y',strtotime(@$edulist->course_start)); ?><span>-</span><?php echo date('M Y',strtotime(@$edulist->course_end)); ?></div>
							<div class="edu_score"><span>Score: <?php echo @$edulist->score; ?> <?php echo @$edulist->ac_score; ?> </span></div>
							<div class="edu_study_area">
								<span><?php echo @$edulist->degree_level; ?></span>
								<span><?php echo @$subjectareadetail->name; ?></span>
								<span><?php echo @$subjectdetail->name; ?></span>
							</div>
						</div>
						<div class="education_action">
							<a data-id="<?php echo $edulist->id; ?>" href="javascript:;" class="editeducation"><i class="fa fa-edit"></i></a>
							<a href="javascript:;" data-id="<?php echo $edulist->id; ?>" class="deleteeducation"><i class="fa fa-trash"></i></a>
						</div>
					</div>
				</div>
			</div>
			<?php
		}
		return ob_get_clean();
	}
	
	public function deleteeducation(Request $request){
		if(Education::where('id', $request->edu_id)->exists()){
			$res = DB::table('education')->where('id', @$request->edu_id)->delete();
			$response['status'] 	= 	true;
				$response['message']	=	'Education deleted Successfully';
		}else{
				$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);	
	}
	
	public function edittestscores(Request $request){
		if(\App\TestScore::where('client_id', $request->client_id)->where('type', $request->type)->exists()){
			$testscores = \App\TestScore::where('client_id', $request->client_id)->where('type', $request->type)->first();
			$obj = \App\TestScore::find($testscores->id);
			$obj->user_id = @Auth::user()->id;
			$obj->client_id = $request->client_id;
			$obj->toefl_Listening = $request->band_score_1_1;
			$obj->toefl_Reading = $request->band_score_2_1;
			$obj->toefl_Writing = $request->band_score_3_1;
			$obj->toefl_Speaking = $request->band_score_4_1;
			$obj->ilets_Listening = $request->band_score_5_2;
			$obj->ilets_Reading = $request->band_score_6_2;
			$obj->ilets_Writing = $request->band_score_7_2;
			$obj->ilets_Speaking = $request->band_score_8_2;
			$obj->pte_Listening = $request->band_score_9_3;
			$obj->pte_Reading = $request->band_score_10_3;
			$obj->pte_Writing = $request->band_score_11_3;
			$obj->pte_Speaking = $request->band_score_12_3;
			$obj->score_3 = $request->score_3;
			$obj->score_2 = $request->score_2;
			$obj->score_1 = $request->score_1;
			
			$obj->toefl_Date = $request->band_score_5_1;
			$obj->ilets_Date = $request->band_score_6_1;
			$obj->pte_Date = $request->band_score_7_1;
			$saved = $obj->save();
		}else{
			$obj = new \App\TestScore;
			$obj->user_id = @Auth::user()->id;
			$obj->client_id = $request->client_id;
			$obj->toefl_Listening = $request->band_score_1_1;
			$obj->toefl_Reading = $request->band_score_2_1;
			$obj->toefl_Writing = $request->band_score_3_1;
			$obj->toefl_Speaking = $request->band_score_4_1;
			$obj->ilets_Listening = $request->band_score_5_2;
			$obj->ilets_Reading = $request->band_score_6_2;
			$obj->ilets_Writing = $request->band_score_7_2;
			$obj->ilets_Speaking = $request->band_score_8_2;
			$obj->pte_Listening = $request->band_score_9_3;
			$obj->pte_Reading = $request->band_score_10_3;
			$obj->pte_Writing = $request->band_score_11_3;
			$obj->pte_Speaking = $request->band_score_12_3;
			$obj->score_3 = $request->score_3;
			$obj->score_2 = $request->score_2;
			$obj->score_1 = $request->score_1;
			$obj->type = $request->type;
			$obj->toefl_Date = $request->band_score_5_1;
			$obj->ilets_Date = $request->band_score_6_1;
			$obj->pte_Date = $request->band_score_7_1;
			$saved = $obj->save();
		}
		
		if($saved){
			$testscores = \App\TestScore::where('client_id', $request->client_id)->where('type', $request->type)->first();
			$toefl_Listening = '-';
			if($testscores->toefl_Listening != ''){
				$toefl_Listening = $testscores->toefl_Listening;
			}
			$toefl_Reading = '-';
			if($testscores->toefl_Reading != ''){
				$toefl_Reading = $testscores->toefl_Reading;
			}
			$toefl_Writing = '-';
			if($testscores->toefl_Writing != ''){
				$toefl_Writing = $testscores->toefl_Writing;
			}
			$toefl_Speaking = '-';
			if($testscores->toefl_Speaking != ''){
				$toefl_Speaking = $testscores->toefl_Speaking;
			}
			$ilets_Listening = '-';
			if($testscores->ilets_Listening != ''){
				$ilets_Listening = $testscores->ilets_Listening;
			}
			$ilets_Reading = '-';
			if($testscores->ilets_Reading != ''){
				$ilets_Reading = $testscores->ilets_Reading;
			}
			$ilets_Writing = '-';
			if($testscores->ilets_Writing != ''){
				$ilets_Writing = $testscores->ilets_Writing;
			}
			
			$ilets_Speaking = '-';
			if($testscores->ilets_Speaking != ''){
				$ilets_Speaking = $testscores->ilets_Speaking;
			}
			$pte_Listening = '-';
			if($testscores->pte_Listening != ''){
				$pte_Listening = $testscores->pte_Listening;
			}
			$pte_Reading = '-';
			if($testscores->pte_Reading != ''){
				$pte_Reading = $testscores->pte_Reading;
			}
			$pte_Writing = '-';
			if($testscores->pte_Writing != ''){
				$pte_Writing = $testscores->pte_Writing;
			}
			$pte_Speaking = '-';
			if($testscores->pte_Speaking != ''){
				$pte_Speaking = $testscores->pte_Speaking;
			}
			$score_3 = '-';
			if($testscores->score_3 != ''){
				$score_3 = $testscores->score_3;
			}
			$score_2 = '-';
			if($testscores->score_2 != ''){
				$score_2 = $testscores->score_2;
			}
			$score_1 = '-';
			if($testscores->score_1 != ''){
				$score_1 = $testscores->score_1;
			}
			$toefl_Date = '-';
			if($testscores->toefl_Date != ''){
				$toefl_Date = $testscores->toefl_Date;
			}
			$ilets_Date = '-';
			if($testscores->ilets_Date != ''){
				$ilets_Date = $testscores->ilets_Date;
			}
			$pte_Date = '-';
			if($testscores->pte_Date != ''){
				$pte_Date = $testscores->pte_Date;
			}
				
			
			$response['status'] 	= 	true;
			$response['message']	=	'Scores updated Successfully';
			$response['score_1']		=	$score_1;
			$response['score_2']		=	$score_2;
			$response['score_3']		=	$score_3;
			$response['pte_Speaking']		=	$pte_Speaking;
			$response['pte_Writing']		=	$pte_Writing;
			$response['pte_Reading']		=	$pte_Reading;
			$response['pte_Listening']		=	$pte_Listening;
			$response['ilets_Speaking']		=	$ilets_Speaking;
			$response['ilets_Writing']		=	$ilets_Writing;
			$response['ilets_Reading']		=	$ilets_Reading;
			$response['ilets_Listening']		=	$ilets_Listening;
			$response['toefl_Speaking']		=	$toefl_Speaking;
			$response['toefl_Writing']		=	$toefl_Writing;
			$response['toefl_Reading']		=	$toefl_Reading;
			$response['toefl_Listening']		=	$toefl_Listening;
			
			$response['toefl_Date']		=	$toefl_Date;
			$response['ilets_Date']		=	$ilets_Date;
			$response['pte_Date']		=	$pte_Date;
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		} 
		echo json_encode($response);	 
	}
	
	public function othertestscores(Request $request){
		if(\App\TestScore::where('client_id', $request->client_id)->exists()){
			$testscores = \App\TestScore::where('client_id', $request->client_id)->first();
			$obj = \App\TestScore::find($testscores->id);
			
			$obj->sat_i = $request->sat_i;
			$obj->sat_ii = $request->sat_ii;
			$obj->gre = $request->gre;
			$obj->gmat = $request->gmat;
			$saved = $obj->save();
		}else{
			$obj = new \App\TestScore;
			$obj->user_id = @Auth::user()->id;
			$obj->client_id = $request->client_id;
			$obj->sat_i = $request->sat_i;
			$obj->sat_ii = $request->sat_ii;
			$obj->gre = $request->gre;
			$obj->gmat = $request->gmat;
			
			$saved = $obj->save();
		}
		
		if($saved){
			$testscores = \App\TestScore::where('client_id', $request->client_id)->first();
			$sat_i = '-';
			if($testscores->sat_i != ''){
				$sat_i = $testscores->sat_i;
			}
			$sat_ii = '-';
			if($testscores->sat_ii != ''){
				$sat_ii = $testscores->sat_ii;
			}
			$gre = '-';
			if($testscores->gre != ''){
				$gre = $testscores->gre;
			}
			$gmat = '-';
			if($testscores->gmat != ''){
				$gmat = $testscores->gmat;
			}
			
			
			$response['status'] 	= 	true;
			$response['message']	=	'Scores updated Successfully';
			$response['gmat']		=	$gmat;
			$response['gre']		=	$gre;
			$response['sat_ii']		=	$sat_ii;
			$response['sat_i']		=	$sat_i;
			
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);	
	}
	
	public function getEducationdetail(Request $request){
		$obj = Education::find($request->id);
		if($obj){
			?>
			<form method="post" action="<?php echo \URL::to('/admin/editeducation'); ?>" name="editeducationform" id="editeducationform" autocomplete="off" enctype="multipart/form-data">
				<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
				<input type="hidden" name="client_id" value="<?php echo $obj->client_id; ?>">
				<input type="hidden" name="id" value="<?php echo $obj->id; ?>">
					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="degree_title">Degree Title <span class="span_req">*</span></label>
								<input type="text" name="degree_title" class="form-control" data-valid="required" autocomplete="off" placeholder="Enter Degree Title" value="<?php echo $obj->degree_title; ?>">
								
								<span class="custom-error degree_title_error" role="alert">
									<strong></strong>
								</span> 
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="degree_level">Degree Level <span class="span_req">*</span></label> 	
								<select data-valid="required" class="form-control degree_level select2" name="degree_level">
									<option value="">Please Select Degree Level</option>
									<option value="Bachelor" <?php if($obj->degree_level == 'Bachelor'){ echo 'selected'; } ?>>Bachelor</option>
									<option value="Certificate" <?php if($obj->degree_level == 'Certificate'){ echo 'selected'; } ?>>Certificate</option>
									<option value="Diploma" <?php if($obj->degree_level == 'Diploma'){ echo 'selected'; } ?>>Diploma</option>
									<option value="High School" <?php if($obj->degree_level == 'High School'){ echo 'selected'; } ?>>High School</option>
									<option value="Master" <?php if($obj->degree_level == 'Master'){ echo 'selected'; } ?>>Master</option>
								</select>
								<span class="custom-error degree_level_error" role="alert">
									<strong></strong>
								</span> 
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="institution">Institution <span class="span_req">*</span></label>
								<input type="text" name="institution" class="form-control" data-valid="required" autocomplete="off" placeholder="Enter Institution" value="<?php echo $obj->institution; ?>">
								
								<span class="custom-error institution_error" role="alert">
									<strong></strong>
								</span> 
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group"> 
								<label for="course_start">Course Start</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<i class="fas fa-calendar-alt"></i>
										</div>
									</div>
									<input type="text" name="course_start" class="form-control datepicker" data-valid="required" autocomplete="off" placeholder="Select Date" value="<?php echo $obj->course_start; ?>">
									
									
								</div>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group"> 
								<label for="course_end">Course End</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<i class="fas fa-calendar-alt"></i>
										</div>
									</div>
									<input type="text" name="course_end" class="form-control datepicker" data-valid="required" autocomplete="off" placeholder="Select Date" value="<?php echo $obj->course_end; ?>">
									
								</div>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="subject_area">Subject Area</label> 	
								<select data-valid="" class="form-control subject_area select2" id="subjectlist" name="subject_area">
									<option value="">Please Select Subject Area</option>
									<?php
									foreach(\App\SubjectArea::all() as $sublist){
										?>
										<option <?php if($obj->subject_area == $sublist->id){ echo 'selected'; } ?> value="<?php echo $sublist->id; ?>"><?php echo $sublist->name; ?></option>
										<?php
									}
									?>
								</select>
								<span class="custom-error subject_area_error" role="alert">
									<strong></strong>
								</span> 
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="subject">Subject</label> 	
								<select data-valid="" class="form-control subject select2" id="subject" name="subject">
									<option value="">Please Select Subject</option>
									<?php
									$subjects = \App\Subject::where('subject_area', $obj->subject_area)->get();
									?>
									<?php
									foreach($subjects as $subjlist){
										?>
										<option <?php if($obj->subject == $subjlist->id){ echo 'selected'; } ?> value="<?php echo $subjlist->id; ?>"><?php echo $subjlist->name; ?></option>
										<?php
									}
									?>
								</select>
								<span class="custom-error subject_error" role="alert">
									<strong></strong>
								</span> 
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group"> 
								<label class="d-block" for="academic_score">Academic Score</label> 
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" id="percentage" value="%" name="academic_score_type" <?php if($obj->ac_score == '%'){ echo 'checked'; } ?>>
									<label class="form-check-label" for="percentage">Percentage</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" id="GPA" <?php if($obj->ac_score == 'GPA'){ echo 'checked'; } ?> value="GPA" name="academic_score_type">
									<label class="form-check-label" for="GPA">GPA</label>
								</div>
								<input type="number" name="academic_score" class="form-control" data-valid="required" autocomplete="off"  value="<?php echo $obj->score; ?>" step="0.01">
								
								<span class="custom-error academic_score_error" role="alert">
									<strong></strong>
								</span> 
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('editeducationform')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form> 
			<?php
		}else{
			?>
			Record Not Found
			<?php
		}
	}
	
}
