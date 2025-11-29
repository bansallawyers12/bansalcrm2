<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Admin;
use App\Quotation;
use App\Template;
use App\TemplateInfo;
 
use PDF; 
use Auth; 
use Config;

class QuotationsController extends Controller
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
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			} */	
		//check authorization end
	
		$query 		= Quotation::where('is_archive', '=', 0); 
		 
		$totalData 	= $query->count();	//for all data
		
		$lists		= $query->sortable(['id' => 'desc'])->paginate(config('constants.limit'));
		
		return view('Admin.quotations.index',compact(['lists', 'totalData'])); 	
			
		//return view('Admin.quotations.index');	 
	}
	
	public function archived(Request $request)
	{
		$query 		= Quotation::where('is_archive', '=', 1); 
		  
		$totalData 	= $query->count();	//for all data

		$lists		= $query->sortable(['id' => 'desc'])->paginate(20);
		
		
		
		return view('Admin.quotations.archived', compact(['lists', 'totalData'])); 	
 
	}
	
	public function client(Request $request){
		if(isset($_GET['template_id']) && $_GET['template_id'] != ''){
			return Redirect::to('/admin/quotations/client/create/'.$request->client_name.'?template_id='.$_GET['template_id']);
		}else{
			return Redirect::to('/admin/quotations/client/create/'.$request->client_name);
		}
		
	}
	public function create(Request $request, $id = Null)
	{
		
			if(isset($id) && !empty($id))
			{
				
			
				if(Admin::where('id', '=', $id)->exists()) 
				{
					$templatedata = array();
					$templateinfo = array();
					if(isset($_GET['template_id']) && $_GET['template_id'] != ''){
					
						$templatedata = Template::find($_GET['template_id']);
						$templateinfo = TemplateInfo::where('quotation_id',$_GET['template_id'])->get();
					}
					$fetchedData = Admin::find($id);
					return view('Admin.quotations.create', compact(['fetchedData','templateinfo','templatedata']));
				}
				else 
				{
					return Redirect::to('/admin/quotations')->with('error', 'Client Not Exist');
				}	
			}
			else
			{
				return Redirect::to('/admin/quotations')->with('error', Config::get('constants.unauthorized'));
			}
		
	}
	
	public function template(Request $request){
		$query 		= Template::where('status', '=', 0); 
		 
		$totalData 	= $query->count();	//for all data
		
		$lists		= $query->sortable(['id' => 'desc'])->paginate(config('constants.limit'));
		return view('Admin.quotations.template.index', compact(['lists','totalData']));
	}
	public function template_create(Request $request){
		return view('Admin.quotations.template.create');
	}
	
	
	public function template_store(Request $request)
	{		
		//check authorization end
		if ($request->isMethod('post')) 
		{
			$this->validate($request, [
										
							'template_name' => 'required|max:255',
									
									  ]);
			
			$requestData 		= 	$request->all();
			
			$obj				= 	new Template;
			$obj->client_id		=	@$requestData['client_id'];
			$obj->user_id		=	@Auth::user()->id;

			$obj->status		=	0;
			$obj->name		=	@$requestData['template_name'];
			$obj->office		=	@$requestData['office'];
		
			$obj->currency	=		@$requestData['quotation_currency'];
			
			
			$saved				=	$obj->save();  
			$workflowid  = $requestData['workflowid'];
			$partnerid  = $requestData['partnerid'];
			$productid  = $requestData['productid'];
			$branchid  = $requestData['branchid'];
			$description  = $requestData['description'];
			$service_fee  = $requestData['service_fee'];
			$discount  = $requestData['discount'];
			$exg_rate  = $requestData['exg_rate'];
			
			for($i=0; $i<count($workflowid); $i++){
				$objs = new \App\TemplateInfo;
				$objs->quotation_id = $obj->id;
				$objs->workflow = $workflowid[$i];
				$objs->partner = $partnerid[$i];
				$objs->product = $productid[$i];
				$objs->branch = $branchid[$i];
				$objs->description = $description[$i];
				$objs->service_fee = $service_fee[$i];
				$objs->discount = $discount[$i];
				$objs->exg_rate = $exg_rate[$i];
				$saveds				=	$objs->save();  
			}
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return Redirect::to('/admin/quotations/template')->with('success', 'Template Created Successfully');
			}				
		}	

	}
	
	public function store(Request $request)
	{		
		//check authorization end
		if ($request->isMethod('post')) 
		{
			$this->validate($request, [
										
										'client_id' => 'required|max:255',
									
									  ]);
			
			$requestData 		= 	$request->all();
			
			$obj				= 	new Quotation;
			$obj->client_id		=	@$requestData['client_id'];
			$obj->user_id		=	@Auth::user()->id;

			$obj->status		=	0;
			$obj->due_date		=	@$requestData['due_date'];
			$obj->created_by	=	@Auth::user()->id;
			$obj->currency	=		@$requestData['quotation_currency'];
			
			
			$saved				=	$obj->save();  
			$workflowid  = $requestData['workflowid'];
			$partnerid  = $requestData['partnerid'];
			$productid  = $requestData['productid'];
			$branchid  = $requestData['branchid'];
			$description  = $requestData['description'];
			$service_fee  = $requestData['service_fee'];
			$discount  = $requestData['discount'];
			$exg_rate  = $requestData['exg_rate'];
			
			for($i=0; $i<count($workflowid); $i++){
				$objs = new \App\QuotationInfo;
				$objs->quotation_id = $obj->id;
				$objs->workflow = $workflowid[$i];
				$objs->partner = $partnerid[$i];
				$objs->product = $productid[$i];
				$objs->branch = $branchid[$i];
				$objs->description = $description[$i];
				$objs->service_fee = $service_fee[$i];
				$objs->discount = $discount[$i];
				$objs->exg_rate = $exg_rate[$i];
				$saveds				=	$objs->save();  
			}
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return Redirect::to('/admin/quotations')->with('success', 'quotations Created Successfully');
			}				
		}	

	}
	
	public function edit(Request $request, $id = NULL)
	{
	
		//check authorization end
		
		if ($request->isMethod('post')) 
		{
			$requestData 		= 	$request->all();
			
			$this->validate($request, [
								//	is_archive	
										'client_id' => 'required|max:255',
									
									  ]);
			
			$requestData 		= 	$request->all();
			//echo '<pre>'; print_r($requestData); die;
			$obj				= 	Quotation::find(@$requestData['id']);
			$obj->client_id		=	@$requestData['client_id'];
			$obj->user_id		=	@Auth::user()->id;

			$obj->status		=	0;
			$obj->due_date		=	@$requestData['due_date'];
			$obj->created_by	=	@Auth::user()->id;
			$obj->currency	=		@$requestData['quotation_currency'];
			
			
			$saved				=	$obj->save();  
			$workflowid  = $requestData['workflowid'];
			$partnerid  = $requestData['partnerid'];
			$productid  = $requestData['productid'];
			$branchid  = $requestData['branchid'];
			$description  = $requestData['description'];
			$service_fee  = $requestData['service_fee'];
			$discount  = $requestData['discount'];
			$exg_rate  = $requestData['exg_rate'];
			DB::table('quotation_infos')->where('quotation_id', @$requestData['id'])->delete();
			for($i=0; $i<count($workflowid); $i++){
				$objs = new \App\QuotationInfo;
				$objs->quotation_id = $obj->id;
				$objs->workflow = $workflowid[$i];
				$objs->partner = $partnerid[$i];
				$objs->product = $productid[$i];
				$objs->branch = $branchid[$i];
				$objs->description = $description[$i];
				$objs->service_fee = $service_fee[$i];
				$objs->discount = $discount[$i];
				$objs->exg_rate = $exg_rate[$i];
				$saveds				=	$objs->save();  
			}
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			
			else
			{
				return Redirect::to('/admin/quotations')->with('success', 'Quotation Edited Successfully');
			}				
		}

		else
		{		
			if(isset($id) && !empty($id))
			{
				
				$id = $this->decodeString($id);	
				if(Quotation::where('id', '=', $id)->exists()) 
				{
					$fetchedData = Quotation::find($id);
					return view('Admin.quotations.edit', compact(['fetchedData']));
				}
				else 
				{
					return Redirect::to('/admin/quotations')->with('error', 'Quotation Not Exist');
				}	
			}
			else
			{
				return Redirect::to('/admin/quotations')->with('error', Config::get('constants.unauthorized'));
			}		
		} 	
		
	}
	
	public function template_edit(Request $request, $id = NULL)
	{
	
		//check authorization end
		
		if ($request->isMethod('post')) 
		{
			$requestData 		= 	$request->all();
			
			$this->validate($request, [
								//	is_archive	
										'template_name' => 'required|max:255',
									
									  ]);
			
			$requestData 		= 	$request->all();
			//echo '<pre>'; print_r($requestData); die;
			$obj				= 	Template::find(@$requestData['id']);
			$obj->client_id		=	@$requestData['client_id'];
			$obj->user_id		=	@Auth::user()->id;

			$obj->status		=	0;
			$obj->name		=	@$requestData['template_name'];
			$obj->office		=	@$requestData['office'];
		
			$obj->currency	=		@$requestData['quotation_currency'];
			
			
			$saved				=	$obj->save();  
			$workflowid  = $requestData['workflowid'];
			$partnerid  = $requestData['partnerid'];
			$productid  = $requestData['productid'];
			$branchid  = $requestData['branchid'];
			$description  = $requestData['description'];
			$service_fee  = $requestData['service_fee'];
			$discount  = $requestData['discount'];
			$exg_rate  = $requestData['exg_rate'];
			DB::table('template_infos')->where('quotation_id', @$requestData['id'])->delete();
			for($i=0; $i<count($workflowid); $i++){
				$objs = new \App\TemplateInfo;
				$objs->quotation_id = $obj->id;
				$objs->workflow = $workflowid[$i];
				$objs->partner = $partnerid[$i];
				$objs->product = $productid[$i];
				$objs->branch = $branchid[$i];
				$objs->description = $description[$i];
				$objs->service_fee = $service_fee[$i];
				$objs->discount = $discount[$i];
				$objs->exg_rate = $exg_rate[$i];
				$saveds				=	$objs->save();  
			}
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			
			else
			{
				return Redirect::to('/admin/quotations/template')->with('success', 'Quotation Edited Successfully');
			}				
		}

		else
		{		
			if(isset($id) && !empty($id))
			{
				
				$id = $this->decodeString($id);	
				if(Template::where('id', '=', $id)->exists()) 
				{
					$fetchedData = Template::find($id);
					return view('Admin.quotations.template.edit', compact(['fetchedData']));
				}
				else 
				{
					return Redirect::to('/admin/quotations/template')->with('error', 'Quotation Not Exist');
				}	
			}
			else
			{
				return Redirect::to('/admin/quotations/template')->with('error', Config::get('constants.unauthorized'));
			}		
		} 	
		
	}
	
	
	public function changestatus(Request $request){
		$requestData 		= 	$request->all();
	
		foreach($requestData['id'] as $l){
			$obj						= 	Quotation::find(@$l);
			$obj->status				= 	1;
			$saved = $obj->save();
			
		}
		
		if(!$saved){
				echo json_encode(array('success' => false));
			}else{
				echo json_encode(array('success' => true));
			}
	}
	public function sendmail(Request $request){
		$requestData = $request->all();
		//echo '<pre>'; print_r($requestData); die;
		$user_id = @Auth::user()->id;
		
		 $obj = new \App\MailReport;
		$obj->user_id =  $user_id;
		$obj->from_mail =  $requestData['email_from'];
		$obj->to_mail =  $requestData['to'];
		if(isset($requestData['email_cc'])){
		$obj->cc =  implode(',',@$requestData['email_cc']);
		}
		$obj->template_id =  $requestData['template'];
		$obj->subject =  $requestData['subject'];
		$obj->message =  $requestData['message'];
		$saved							=	$obj->save(); 
		
		$subject = $requestData['subject'];
		$message = $requestData['message'];
		$explode = explode(',', $requestData['to']);
		
		
		$quoto = explode(',', $requestData['quoto']);
		$i = 0;
		foreach($explode as $l){
			$fetchedData						= 	Quotation::find(@$quoto[$i]);
			$reciept_id = $fetchedData->id;
				 $pdf = PDF::setOptions([
					'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
					'logOutputFile' => storage_path('logs/log.htm'),
					'tempDir' => storage_path('logs/')
					])->loadView('emails.quotaion', compact('fetchedData')); 
				$output = $pdf->output();
				$invoicefilename = 'Quotation_'.$reciept_id.'.pdf';
				file_put_contents('/home/digitrex/crm.digitrex.live/public/invoices/'.$invoicefilename, $output);
				$array['file'] = '/home/digitrex/crm.digitrex.live/public/invoices/'.$invoicefilename;
				$array['file_name'] = $invoicefilename;
			
			$client = \App\Admin::Where('id', $l)->first();
			$subject = str_replace('{Client First Name}',$client->first_name, $subject);
			$message = str_replace('{Client First Name}',$client->first_name, $message);
			$message = str_replace('{Client Assignee Name}',$client->first_name, $message);
			$message = str_replace('{Company Name}',Auth::user()->company_name, $message);
			
				$this->send_compose_template($client->email, $subject, 'support@digitrex.live', $message, 'digitrex', $array);
			//	$this->send_compose_template($client->email, $subject, 'support@digitrex.live', $message, 'digitrex');
			
			unset($array['file']);
			$i++;
		}
		
		foreach($quoto as $l){
			$obj						= 	Quotation::find(@$l);
			$obj->status				= 	3;
			$saved = $obj->save();
			
		}
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			
			else
			{
				return Redirect::to('/admin/quotations')->with('success', 'Email Sent Successfully');
			}	
	}
	
	public function quotationDetail(Request $request, $id = Null){
		if(isset($id) && !empty($id))
			{
				
				$id = $this->decodeString($id);	
				if(Quotation::where('id', '=', $id)->exists()) 
				{
					$fetchedData = Quotation::find($id);
					return view('Admin.quotations.detail', compact(['fetchedData']));
				}
				else 
				{
					return Redirect::to('/admin/quotations')->with('error', 'Quotation Not Exist');
				}	
			}
			else
			{
				return Redirect::to('/admin/quotations/template')->with('error', Config::get('constants.unauthorized'));
			}
	}
	
	public function quotationpreview(Request $request, $id = Null){
		if(isset($id) && !empty($id))
			{
				
				$id = $this->decodeString($id);	
				if(Quotation::where('id', '=', $id)->exists()) 
				{
					$fetchedData = Quotation::find($id);
					//return view('emails.quotaion', compact(['fetchedData']));
					$pdf = PDF::setOptions([
					'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
					'logOutputFile' => storage_path('logs/log.htm'),
					'tempDir' => storage_path('logs/')
					])->loadView('emails.quotaion', compact(['fetchedData'])); 
					//
					return $pdf->stream('REC '.$fetchedData->id.'.pdf');
				}
				else 
				{
					return Redirect::to('/admin/quotations')->with('error', 'Quotation Not Exist');
				}	
			}
			else
			{
				return Redirect::to('/admin/quotations/template')->with('error', Config::get('constants.unauthorized'));
			}
	}
}
