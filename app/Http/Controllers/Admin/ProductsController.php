<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Models\Admin;
use App\Models\Product;
// Removed: ProductAreaLevel, FeeOption, FeeOptionType - tables dropped
 
use Auth;
use Config;

class ProductsController extends Controller
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
		//check authorization start	
			
			/* if($check)
			{
				return redirect()->route('dashboard')->with('error',config('constants.unauthorized'));
			} */	
	//check authorization end
	
	$query 		= Product::query();
		  
		$totalData 	= $query->count();	//for all data
		if ($request->has('name')) 
		{
			$name 		= 	$request->input('name'); 
			if(trim($name) != '')
			{
				$query->where('name', 'ilike', '%'.$name.'%');
					
			}
		}
		if ($request->has('branch')) 
		{
			$branch 		= 	$request->input('branch'); 
			if(trim($branch) != '')
			{
				$query->whereHas('branchdetail', function ($q) use($branch){
					$q->where('name','ilike', '%'.$branch.'%');
				});
					
			}
		}
		if ($request->has('branch')) 
		{
			$branch 		= 	$request->input('branch'); 
			if(trim($branch) != '')
			{
				$query->whereHas('branchdetail', function ($q) use($branch){
					$q->where('name','ilike', '%'.$branch.'%');
				});
					
			}
		}
		if ($request->has('partner')) 
		{
			$branch 		= 	$request->input('partner'); 
			if(trim($branch) != '')
			{
				$query->whereHas('partnerdetail', function ($q) use($branch){
					$q->where('partner_name','ilike', '%'.$branch.'%');
				});
					
			}
		}
		
		$lists		= $query->sortable(['id' => 'desc'])->paginate(20);
		
		
		return view('Admin.products.index', compact(['lists', 'totalData'])); 	
				
		//return view('Admin.products.index'); 	 
	}
	
	
	public function create(Request $request)
	{
		//check authorization end
		//return view('Admin.users.create',compact(['usertype']));	
		
		return view('Admin.products.create');	
	}
	
	public function store(Request $request)
	{		
		//check authorization end
		if ($request->isMethod('post')) 
		{
			$this->validate($request, [
										'name' => 'required|max:255'
									  ]);
			
			$requestData 		= 	$request->all();
			 
			$obj				= 	new Product; 
			$obj->name	=	@$requestData['name'];
			$obj->partner	=	@$requestData['partner'];
			$obj->branches	=	@$requestData['branches'];
			$obj->product_type	=	@$requestData['product_type'];
			$obj->revenue_type	=	@$requestData['revenue_type']; 
			$obj->duration	=	@$requestData['duration'];
			$obj->intake_month	=	@$requestData['intake_month'];
			$obj->description	=	@$requestData['description'];
			$obj->note	=	@$requestData['note'];
			
			$saved				=	$obj->save();  
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return redirect()->route('products.index')->with('success', 'Products Added Successfully');
			}				
		}	

		return view('Admin.products.create');	 
	}
	
	public function edit(Request $request, $id = NULL)
	{
	
		//check authorization end
		
		if ($request->isMethod('post')) 
		{
			$requestData 		= 	$request->all();
			
			$this->validate($request, [										
										'name' => 'required|max:255'
									  ]);
								  					  
			$obj							= 	Product::find(@$requestData['id']);
						
			$obj->name	=	@$requestData['name'];
			$obj->partner	=	@$requestData['partner'];
			$obj->branches	=	@$requestData['branches'];
			$obj->product_type	=	@$requestData['product_type'];
			$obj->revenue_type	=	@$requestData['revenue_type']; 
			$obj->duration	=	@$requestData['duration'];
			$obj->intake_month	=	@$requestData['intake_month'];
			$obj->description	=	@$requestData['description'];
			$obj->note	=	@$requestData['note'];
			
			$saved							=	$obj->save();
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			
			else
			{
				return redirect()->route('products.index')->with('success', 'Products Edited Successfully');
			}				
		}

		else
		{		
			if(isset($id) && !empty($id))
			{
				
				$id = $this->decodeString($id);	
				if(Product::where('id', '=', $id)->exists()) 
				{
					$fetchedData = Product::find($id);
					return view('Admin.products.edit', compact(['fetchedData']));
				}
				else 
				{
					return redirect()->route('products.index')->with('error', 'Products Not Exist');
				}	
			}
			else
			{
				return redirect()->route('products.index')->with('error', Config::get('constants.unauthorized'));
			}		
		} 	
		
	}
	
	public function detail(Request $request, $id = NULL, $tab = NULL){
		if(isset($id) && !empty($id))  
			{				
				$id = $this->decodeString($id);	
				if(Product::where('id', '=', $id)->exists()) 
				{ 
					$fetchedData = Product::find($id);
					return view('Admin.products.detail', compact(['fetchedData']));
				}
				else 
				{  
					return redirect()->route('products.index')->with('error', 'Products Not Exist');
				}	
			}
			else
			{
				return redirect()->route('products.index')->with('error', Config::get('constants.unauthorized'));
			}
	}
	
	public function getrecipients(Request $request){
		$squery = $request->q;
		if($squery != ''){
			
			 $partners = \App\Models\Admin::where('is_archived', '=', 0)
       ->where('role', '=', 7)
       ->where(
           function($query) use ($squery) {
             return $query
                    ->where('email', 'ilike', '%'.$squery.'%')
                    ->orwhere('partner_name', 'ilike','%'.$squery.'%');
            })
            ->get();
			
			$items = array();
			foreach($partners as $partner){
				$items[] = array('partner_name' => $partner->partner_name,'email'=>$partner->email,'status'=>'Partner','id'=>$partner->id,'cid'=>base64_encode(convert_uuencode(@$partner->id)));
			}
			
			echo json_encode(array('items'=>$items));
		}
	}
	
	
	public function getallproducts(Request $request){
		$squery = $request->q;
		if($squery != ''){
			
			 $partners = \App\Models\Admin::where('is_archived', '=', 0)
       ->where('role', '=', 7)
       ->where( 
           function($query) use ($squery) {
             return $query
                    ->where('email', 'ilike', '%'.$squery.'%')
                    ->orwhere('partner_name', 'ilike','%'.$squery.'%');
            })
            ->get();
			
			$items = array();
			foreach($partners as $partner){ 
				$items[] = array('partner_name' => $partner->partner_name,'email'=>$partner->email,'status'=>'Partner','id'=>$partner->id,'cid'=>base64_encode(convert_uuencode(@$partner->id)));
			}
			
			echo json_encode(array('items'=>$items));
		}
	}
	
	// Removed: saveotherinfo - product_area_levels table dropped
	public function saveotherinfo(Request $request){
		$response['status'] = false;
		$response['message'] = 'Feature removed - product_area_levels table no longer exists';
		echo json_encode($response);
	}
	
	
	// Removed: getotherinfo - product_area_levels table dropped
	public function getotherinfo(Request $request){
		// ProductAreaLevel table dropped - return empty form
		$ac = null;
		ob_start();
		?>
		<div class="col-12 col-md-6 col-lg-6">
						<div class="form-group">
							<label for="degree_level">Subject Area</label> 	
							<select data-valid="" class="form-control subject_area select2" id="subjectlist" name="subject_area">
									<option value="">Please Select Subject Area</option>
									<!-- Subject Area dropdown removed - subject_areas table has been dropped -->
								</select>
							<span class="custom-error degree_level_error" role="alert">
								<strong></strong>
							</span> 
						</div>
					</div>
					<div class="col-12 col-md-6 col-lg-6">
						<div class="form-group">
							<label for="degree_level">Subject<span class="span_req">*</span></label> 	
							<select data-valid="" class="form-control subject select2" id="subject" name="subject">
									<option value="">Please Select Subject</option>
									<?php
									foreach(\App\Models\Subject::where('subject_area',$ac->subject_area) ->orderby('name','ASC')->get() as $sublist){
										?>
										<option <?php if($ac->subject == $sublist->id){ echo 'selected'; } ?> value="<?php echo $sublist->id; ?>"><?php echo $sublist->name; ?></option>
										<?php
									}
									?>
								</select>
							<span class="custom-error degree_level_error" role="alert">
								<strong></strong>
							</span> 
						</div>
					</div>
					<div class="col-12 col-md-6 col-lg-6">
						<div class="form-group">
							<label for="degree_level">Degree Level</label> 	
							<select data-valid="required" class="form-control degree_level select2" name="degree_level">
								<option value=""></option>
								<option <?php if($ac->degree == 'Bachelor'){ echo 'selected'; } ?> value="Bachelor">Bachelor</option>
									<option value="Certificate" <?php if($ac->degree == 'Certificate'){ echo 'selected'; } ?>>Certificate</option>
									<option value="Diploma" <?php if($ac->degree == 'Diploma'){ echo 'selected'; } ?>>Diploma</option>
									<option value="High School" <?php if($ac->degree == 'High School'){ echo 'selected'; } ?>>High School</option>
									<option value="Master" <?php if($ac->degree == 'Master'){ echo 'selected'; } ?>>Master</option>
							</select>
							<span class="custom-error degree_level_error" role="alert">
								<strong></strong>
							</span> 
						</div>
					</div>
					<div class="col-12 col-md-12 col-lg-12">
						<button onclick="customValidate('editsubjectarea')" type="button" class="btn btn-primary">Save</button>
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					</div>
		<?php
		return ob_get_clean();
		
	}
	
	
	// Removed: savefee - fee_options and fee_option_types tables dropped
	public function savefee(Request $request){
		$response['status'] = false;
		$response['message'] = 'Feature removed - fee_options table no longer exists';
		echo json_encode($response);
	}
	
	// Removed: getallfees - fee_options table dropped
	public function getallfees(Request $request){
		// FeeOption table dropped - return empty
		$feeoptions = collect([]);
		ob_start();
		foreach($feeoptions as $feeoption){
			
	?>
		<div class="feeitem">
			<div class="row">
				<div class="col-md-10">
					<h4 class="text-info"><?php echo $feeoption->name; ?></h4>
				</div>
				<div class="col-md-2">
					<a href="javascript:;" class="editfeeoption" data-id="<?php echo $feeoption->id; ?>"><i class="fa fa-edit"></i></a>
					<a href="javascript:;" class="deletenote" data-href="deletefee" data-id="<?php echo $feeoption->id; ?>"><i class="fa fa-trash"></i></a>
				</div>
				<div class="col-md-2">
					<div class="validfor">
						<span>Valid For</span><br>
						<div class=""><b><?php echo $feeoption->country; ?></b></div>
					</div>
					<div class="installmenttype">
						<span>Installment Type</span><br>
						<div class=""><b><?php echo $feeoption->installment_type; ?></b></div>
					</div>
				</div>
				<?php
				// FeeOptionType table dropped
				$feeoptiontype = collect([]);
				
				?>
				<div class="col-md-8">
					<div class="validfor">
						<span>Fee Breakdown</span><br>
					<?php $totlfee = 0; foreach($feeoptiontype as $feeoptiontyp){
						$totlfee += $feeoptiontyp->total_fee;
						?>
						<div class="">
							<span><b><?php echo $feeoptiontyp->fee_type; ?></b></span><span> <?php echo $feeoptiontyp->installment; ?> Per Month @ AUD <?php echo $feeoptiontyp->inst_amt; ?></span><span style="margin-left: 24px;"><b>AUD <?php echo $feeoptiontyp->total_fee; ?></b></span>
							
						</div>
					<?php } ?>
					</div>
					
				</div>
				
				<div class="col-md-2">
					<div class="validfor">
						<span>Total Fees</span><br>
						<div class="text-info"><h4>AUD</h4></div>
					
						<div class="text-info"><h4><?php echo number_format($totlfee,2,'.',''); ?></h4></div>
					</div>
					
				</div>
			</div>
			<hr>
		</div>
	<?php }
return ob_get_clean();	
	}
	
	// Removed: editfee - fee_options table dropped
	public function editfee(Request $request){
		// FeeOption table dropped
		$fetchedData = null;
		if($fetchedData){
			ob_start();
			?>
			<form method="post" action="<?php echo \URL::to('/editfee'); ?>" name="editfeeform" id="editfeeform" autocomplete="off" enctype="multipart/form-data">
				<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
				<input type="hidden" name="id" value="<?php echo $fetchedData->id; ?>">
				<input type="hidden" name="product_id" value="<?php echo $fetchedData->product_id; ?>">
					<div class="row">
						<div class="col-12 col-md-4 col-lg-4">
							<div class="form-group">
								<label for="fee_option_name">Fee Option Name <span class="span_req">*</span></label> 	
								<input type="text" value="<?php echo $fetchedData->name; ?>" class="form-control selectedappsubject" data-valid="required" placeholder="Enter Fee Option Name" name="fee_option_name">
								
								<span class="custom-error feeoption_name_error" role="alert">
									<strong></strong>
								</span> 
							</div>
						</div>
						<div class="col-12 col-md-4 col-lg-4">
							<div class="form-group">
								<label for="country_residency">Country of Residency <span class="span_req">*</span></label> 
								<select class="form-control residencyelect2" name="country_residency" data-valid="required">
								<option value="">Select Country</option>
								<?php
									foreach(\App\Models\Country::all() as $list){
										?>
										<option <?php if($fetchedData->country == $list->name){ echo 'selected'; } ?> value="<?php echo @$list->name; ?>"><?php echo @$list->name; ?></option>
										<?php
									}
									?>
								</select>
								<span class="custom-error country_residency_error" role="alert">
									<strong></strong>
								</span> 
							</div>
						</div>
						<div class="col-12 col-md-4 col-lg-4">
							<div class="form-group"> 
								<label for="degree_level">Installment Type <span class="span_req">*</span></label> 
								<select data-valid="required" class="form-control degree_level edit_installment_type select2" name="degree_level">
									<option value="">Select Type</option>
									<option value="Full Fee" <?php if($fetchedData->installment_type == "Full Fee"){ echo 'selected'; } ?>>Full Fee</option>
									<option value="Per Year" <?php if($fetchedData->installment_type == "Per Year"){ echo 'selected'; } ?>>Per Year</option>
									<option value="Per Month" <?php if($fetchedData->installment_type == "Per Month"){ echo 'selected'; } ?>>Per Month</option>
									<option value="Per Term" <?php if($fetchedData->installment_type == "Per Term"){ echo 'selected'; } ?>>Per Term</option>
									<option value="Per Trimester" <?php if($fetchedData->installment_type == "Per Trimester"){ echo 'selected'; } ?>>Per Trimester</option>
									<option value="Per Semester" <?php if($fetchedData->installment_type == "Per Semester"){ echo 'selected'; } ?>>Per Semester</option>
									<option value="Per Week" <?php if($fetchedData->installment_type == "Per Week"){ echo 'selected'; } ?>>Per Week</option>
									<option value="Installment" <?php if($fetchedData->installment_type == "Installment"){ echo 'selected'; } ?>>Installment</option>
								</select>
								<span class="custom-error degree_level_error" role="alert">
									<strong></strong>
								</span> 
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="table-responsive"> 
								<table class="table text_wrap" id="productitemview">
									<thead>
										<tr> 
											<th>Fee Type <span class="span_req">*</span></th>
											<th>Installment Amount <span class="span_req">*</span></th>
											<th>Installments <span class="span_req">*</span></th>
											<th>Total Fee</th>
											<th>Claimable Terms</th>
											<th>Commission %</th>
											<th>Add in quotation</th>
										</tr> 
									</thead>
									<tbody class="tdata">
									<?php
									$total_fee = 0;
									$i = 0;
											// FeeOptionType table dropped
											$feeoptiontypes = collect([]);
												foreach($feeoptiontypes as $feeoptiontype){
													$total_fee += $feeoptiontype->total_fee;
									?>
										<tr class="add_fee_option cus_fee_option">
											<td>
												<select data-valid="required" class="form-control course_fee_type " name="course_fee_type[]">
													<option value="">Select Type</option>
													<?php foreach(\App\Models\FeeType::all() as $feetypes){ ?>
													<option <?php if($feeoptiontype->fee_type == $feetypes->name){ echo 'selected'; } ?> value="<?php echo $feetypes->name; ?>"><?php echo $feetypes->name; ?></option>
													<?php } ?>
												
												</select>
											</td>
											<td>
												<input type="number" value="<?php echo $feeoptiontype->inst_amt; ?>" class="form-control installment_amount" name="installment_amount[]">
											</td>
											<td>
												<input type="number" value="<?php echo $feeoptiontype->installment; ?>" class="form-control installment" name="installment[]">
											</td>
											<td class="total_fee"><span><?php echo $feeoptiontype->total_fee; ?></span><input type="hidden"  class="form-control total_fee_am" value="<?php echo $feeoptiontype->total_fee; ?>" name="total_fee[]"></td>
											<td>
												<input type="number" value="<?php echo $feeoptiontype->claim_term; ?>" class="form-control claimable_terms" name="claimable_terms[]">
											</td>
											<td>
												<input type="number" value="<?php echo $feeoptiontype->commission; ?>" class="form-control commission" name="commission[]">
											</td>
											<td>
												<input value="1" <?php if($feeoptiontype->quotation == 1){ echo 'checked'; } ?> class="add_quotation" type="checkbox" name="add_quotation[]">
												<?php if($i != 0){ ?>
												<a href="javascript:;" class="removefeetype"><i class="fa fa-trash"></i></a>
												<?php } ?>
											</td>
									
										</tr>
										<?php $i++; } ?>
									</tbody>
									<tfoot>
										<tr>
											
											<td colspan="3" style="text-align: right;"><b>Net Total</b></td>
											<td class="net_totl text-info"><?php echo number_format($total_fee,2,'.',''); ?></td>
											<td colspan="3"></td>
										</tr>
									</tfoot>
								</table>	
							</div>
							<div class="fee_option_addbtn">
								<a href="#" class="btn btn-primary"><i class="fas fa-plus"></i> Add Fee</a>
							</div>
							
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('editfeeform')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			<?php
			return ob_get_clean();
		}else{
			echo '<h4>Record Not FOund</h4>';
		}
		die;
	}
	
	
	// Removed: editfeeform - fee_options and fee_option_types tables dropped
	public function editfeeform(Request $request){
		$response['status'] = false;
		$response['message'] = 'Feature removed - fee_options table no longer exists';
		echo json_encode($response);
	}
	
	// Removed: deletefee - fee_options and fee_option_types tables dropped
	public function deletefee(Request $request){
		$response['status'] = false;
		$response['message'] = 'Feature removed - fee_options table no longer exists';
		echo json_encode($response);
	}
}
