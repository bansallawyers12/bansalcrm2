<?php

namespace App\Http\Controllers\Admin\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Auth;
use DataTables;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

use App\Models\Admin;
use App\Models\ActivitiesLog;
use App\Models\AccountClientReceipt;
use App\Models\Document;
use App\Models\Application;

/**
 * Client receipts, invoices, and commission reports
 * 
 * Methods moved from ClientsController:
 * - saveaccountreport
 * - clientreceiptlist
 * - getTopReceiptValInDB
 * - printpreview
 * - getClientReceiptInfoById
 * - validate_receipt
 * - commissionreport
 * - getcommissionreport
 */
class ClientReceiptController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Save client account reports
     */
    public function saveaccountreport(Request $request, $id = NULL)
	{
		$requestData = $request->all();
        
        // Validate function_type is set
        if(!isset($requestData['function_type']) || empty($requestData['function_type'])) {
            $response['status'] = false;
            $response['message'] = 'Invalid request: function_type not specified';
            echo json_encode($response);
            return;
        }
        
        if( $requestData['function_type'] == 'add')
        {
            if ($request->hasfile('document_upload'))
            {
                if(!is_array($request->file('document_upload'))){
                    $files[] = $request->file('document_upload');
                }else{
                    $files = $request->file('document_upload');
                }

                $client_info = Admin::select('client_id')->where('id', $requestData['client_id'])->first();
                if(!empty($client_info)){
                    $client_unique_id = $client_info->client_id;
                } else {
                    $client_unique_id = "";
                }

                $doctype = isset($request->doctype)? $request->doctype : '';

                foreach ($files as $file) {
                    $size = $file->getSize();
                    $fileName = $file->getClientOriginalName();
                    $nameWithoutExtension = pathinfo($fileName, PATHINFO_FILENAME);
                    $fileExtension = $file->getClientOriginalExtension();
                  
                    $name = time() . $file->getClientOriginalName();
                  
                    $filePath = $client_unique_id.'/'.$doctype.'/'. $name;
                    Storage::disk('s3')->put($filePath, file_get_contents($file));

                    $obj = new Document;
                    $obj->file_name = $nameWithoutExtension;
                    $obj->filetype = $fileExtension;
                    $obj->user_id = Auth::user()->id;
                  
                    $fileUrl = Storage::disk('s3')->url($filePath);
                    $obj->myfile = $fileUrl;
                    $obj->myfile_key = $name;

                    $obj->client_id = $requestData['client_id'];
                    $obj->type = $request->type;
                    $obj->file_size = $size;
                    $obj->doc_type = $doctype;
                    $doc_saved = $obj->save();

                    $insertedDocId = $obj->id;
                }

               
            } else {
                $insertedDocId = null;
                $doc_saved = "";
            }

            if(isset($requestData['trans_date'])){
                $finalArr = array();
                for($i=0; $i<count($requestData['trans_date']); $i++){
                    $finalArr[$i]['trans_date'] = $requestData['trans_date'][$i];
                    $finalArr[$i]['entry_date'] = $requestData['entry_date'][$i];
                    $finalArr[$i]['payment_method'] = $requestData['payment_method'][$i];
                    $finalArr[$i]['description'] = $requestData['description'][$i];
                    $finalArr[$i]['deposit_amount'] = $requestData['deposit_amount'][$i];

                    $saved	= DB::table('account_client_receipts')->insertGetId([
                        'user_id' => $requestData['loggedin_userid'],
                        'client_id' =>  $requestData['client_id'],
                        'receipt_type' => $requestData['receipt_type'],
                        'trans_date' => $requestData['trans_date'][$i],
                        'entry_date' => $requestData['entry_date'][$i],
                        'payment_method' => $requestData['payment_method'][$i],
                        'description' => $requestData['description'][$i],
                        'deposit_amount' => $requestData['deposit_amount'][$i],
                        'uploaded_doc_id'=> $insertedDocId,
                        'validate_receipt' => 0,
                        'void_invoice' => 0,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
            if($saved) {
                $requestData['trans_no'][0] = "Rec".$saved;
                $finalArr[0]['trans_no'] = "Rec".$saved;
                $receipt_id = $saved;
                DB::table('account_client_receipts')->where('id',$saved)->update(['trans_no' => $requestData['trans_no'][0],'receipt_id'=>$receipt_id]);
                $response['status'] = true;
                $response['requestData'] = $finalArr;
                $response['lastInsertedId'] = $saved;
                $response['function_type'] = $requestData['function_type'];

                $db_total_deposit_amount = DB::table('account_client_receipts')->where('client_id',$requestData['client_id'])->where('receipt_type',1)->sum('deposit_amount');
                $response['db_total_deposit_amount'] = $db_total_deposit_amount;
              
                $validate_receipt_info = DB::table('account_client_receipts')->select('validate_receipt')->where('id',$saved)->first();
                $response['validate_receipt'] = $validate_receipt_info->validate_receipt;

                if($doc_saved){
                    $awsUrl = $fileUrl;
                    
                    $response['awsUrl'] = $awsUrl;
                    $response['message'] = 'Client receipt with document added successfully';
					$subject = 'added client receipt with Receipt Id-'.$receipt_id.' and Trans. No	-'.$requestData['trans_no'][0].' and document' ;
                } else {
                    $response['message'] = 'Client receipt added successfully';
                    $response['awsUrl'] =  "";
                    $subject = 'added client receipt with Receipt Id-'.$receipt_id.' and Trans. No	-'.$requestData['trans_no'][0];
                }

                $printUrl = \URL::to('/clients/printpreview').'/'.$receipt_id;
                $response['printUrl'] = $printUrl;

                if($request->type == 'client'){
                    $objs = new ActivitiesLog;
                    $objs->client_id = $requestData['client_id'];
                    $objs->created_by = Auth::user()->id;
                    $objs->description = '';
                    $objs->subject = $subject;
                    $objs->task_status = 0;
                    $objs->pin = 0;
                    $objs->save();
                }
            } else {
                $response['lastInsertedId'] = "";
                $response['awsUrl'] =  "";
                $response['requestData'] = "";
                $response['status'] 	= 	false;
                $response['message']	=	'Please try again';
                $response['function_type'] = $requestData['function_type'];
              	$response['validate_receipt'] = "";
            }
        }
        else if( $requestData['function_type'] == 'edit'){
             if ($request->hasfile('document_upload'))
            {
                if(!is_array($request->file('document_upload'))){
                    $files[] = $request->file('document_upload');
                }else{
                    $files = $request->file('document_upload');
                }

                $client_info = Admin::select('client_id')->where('id', $requestData['client_id'])->first();
                if(!empty($client_info)){
                    $client_unique_id = $client_info->client_id;
                } else {
                    $client_unique_id = "";
                }

                $doctype = isset($request->doctype)? $request->doctype : '';

                foreach ($files as $file) {
                    $size = $file->getSize();
                    $fileName = $file->getClientOriginalName();
                    $nameWithoutExtension = pathinfo($fileName, PATHINFO_FILENAME);
                    $fileExtension = $file->getClientOriginalExtension();
                  
                    $name = time() . $file->getClientOriginalName();
                  
                    $filePath = $client_unique_id.'/'.$doctype.'/'. $name;
                    Storage::disk('s3')->put($filePath, file_get_contents($file));

                    $obj = new Document;
                    $obj->file_name = $nameWithoutExtension;
                    $obj->filetype = $fileExtension;
                    $obj->user_id = Auth::user()->id;
                  
                    $fileUrl = Storage::disk('s3')->url($filePath);
                    $obj->myfile = $fileUrl;
                    $obj->myfile_key = $name;
                  
                    $obj->client_id = $requestData['client_id'];
                    $obj->type = $request->type;
                    $obj->file_size = $size;
                    $obj->doc_type = $doctype;
                    $doc_savedL = $obj->save();

                    $insertedDocIdL = $obj->id;
                }
            } else {
                $uploaded_doc_Info1 = DB::table('account_client_receipts')->select('uploaded_doc_id')->where('id',$requestData['id'][0])->first();
                if($uploaded_doc_Info1 && $uploaded_doc_Info1->uploaded_doc_id){
                    $insertedDocIdL = $uploaded_doc_Info1->uploaded_doc_id;
                } else {
                    $insertedDocIdL = null;
                }
                $doc_savedL = "";
            }

            $finalArr = array();
            for($j=0; $j<count($requestData['trans_date']); $j++){
                $finalArr[$j]['trans_date'] = $requestData['trans_date'][$j];
                $finalArr[$j]['entry_date'] = $requestData['entry_date'][$j];
                $finalArr[$j]['payment_method'] = $requestData['payment_method'][$j];
                $finalArr[$j]['description'] = $requestData['description'][$j];
                $finalArr[$j]['deposit_amount'] = $requestData['deposit_amount'][$j];
                $finalArr[$j]['id'] = $requestData['id'][$j];

                $savedDB = DB::table('account_client_receipts')
                    ->where('id',$requestData['id'][$j])
                    ->update([
                        'user_id' => $requestData['loggedin_userid'],
                        'client_id' =>  $requestData['client_id'],
                        'trans_date' => $requestData['trans_date'][$j],
                        'entry_date' => $requestData['entry_date'][$j],
                        'payment_method' => $requestData['payment_method'][$j],
                        'description' => $requestData['description'][$j],
                        'deposit_amount' => $requestData['deposit_amount'][$j],
                        'uploaded_doc_id'=> $insertedDocIdL,
                        'updated_at' => now()
                    ]);
            }
            if($savedDB >=0) {
                $requestData['trans_no'][0] = "Rec".$requestData['id'][0];
                $finalArr[0]['trans_no'] = "Rec".$requestData['id'][0];
                $response['function_type'] = $requestData['function_type'];
                $response['requestData'] 	= $finalArr;
                $db_total_deposit_amount = DB::table('account_client_receipts')->where('client_id',$requestData['client_id'])->where('receipt_type',1)->sum('deposit_amount');
                $response['db_total_deposit_amount'] = $db_total_deposit_amount;
                $response['status'] 	= 	true;

                $response['message'] = 'Client receipt updated successfully';
                $subject = 'updated client receipt with Receipt Id-'.$requestData['id'][0].' and Trans. No-'.$requestData['trans_no'][0];
                $response['lastInsertedId'] = $requestData['id'][0];
              
                $validate_receipt_info = DB::table('account_client_receipts')->select('validate_receipt')->where('id',$requestData['id'][0])->first();
                $response['validate_receipt'] = $validate_receipt_info->validate_receipt;
              
                if($doc_savedL){
                  	$awsUrl = $fileUrl;
                    $response['awsUrl'] = $awsUrl;
                } else {
                    $uploaded_doc_Info = DB::table('account_client_receipts')->select('uploaded_doc_id')->where('id',$requestData['id'][0])->first();
                    if($uploaded_doc_Info){
                        $document_info = DB::table('documents')->select('myfile')->where('id',$uploaded_doc_Info->uploaded_doc_id)->first();
                        if($document_info){
                            $client_info = Admin::select('client_id')->where('id', $requestData['client_id'])->first();
                            if(!empty($client_info)){
                                $client_unique_id = $client_info->client_id;
                            } else {
                                $client_unique_id = "";
                            }
                            $doctype = 'client_receipt';
                            $url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                            $awsUrl = $url.$client_unique_id.'/'.$doctype.'/'.$document_info->myfile;
                            $response['awsUrl'] =  $awsUrl;
                        } else {
                            $response['awsUrl'] =  "";
                        }
                    } else {
                        $response['awsUrl'] =  "";
                    }
                }

                $printUrl = \URL::to('/clients/printpreview').'/'.$requestData['id'][0];
                $response['printUrl'] = $printUrl;

                if($request->type == 'client'){
                    $objs = new ActivitiesLog;
                    $objs->client_id = $requestData['client_id'];
                    $objs->created_by = Auth::user()->id;
                    $objs->description = '';
                    $objs->subject = $subject;
                    $objs->task_status = 0;
                    $objs->pin = 0;
                    $objs->save();
                }
            } else {
                $response['requestData'] = "";
                $response['status'] 	= 	false;
                $response['message']	=	'Please try again';
                $response['function_type'] = $requestData['function_type'];
                $response['lastInsertedId'] = "";
                $response['validate_receipt'] = "";
                $response['awsUrl'] =  "";
            }
        }
        else {
            $response['status'] = false;
            $response['message'] = 'Invalid operation type: ' . ($requestData['function_type'] ?? 'not specified');
            $response['function_type'] = $requestData['function_type'] ?? '';
        }
        echo json_encode($response);
    }

    /**
     * Get top receipt value in DB
     */
    public function getTopReceiptValInDB(Request $request)
	{
        $requestData = 	$request->all();
        $receipt_type = $requestData['type'];
        $record_count = DB::table('account_client_receipts')->where('receipt_type',$receipt_type)->max('id');
        if($record_count) {
            if($receipt_type == 3){
                $max_receipt_id = DB::table('account_client_receipts')->where('receipt_type',$receipt_type)->max('receipt_id');
                $response['max_receipt_id'] 	= $max_receipt_id;
            } else {
                $response['max_receipt_id'] 	= "";
            }
            $response['receipt_type'] 	= $receipt_type;
            $response['record_count'] 	= $record_count;
            $response['status'] 	= 	true;
            $response['message']	=	'Record is exist';
        }else{
            $response['receipt_type'] 	= $receipt_type;
            $response['record_count'] 	= $record_count;
            $response['max_receipt_id'] 	= "";
            $response['status'] 	= 	false;
            $response['message']	=	'Record is not exist.Please try again';
        }
        echo json_encode($response);
    }

    /**
     * Client receipt list
     */
    public function clientreceiptlist(Request $request)
	{
        $query = DB::table('account_client_receipts as acr')
        ->join('admins as ad', 'acr.client_id', '=', 'ad.id')
        ->select('acr.id','acr.receipt_id',
        'acr.client_id','acr.user_id','acr.trans_date','acr.entry_date','acr.trans_no', 'acr.payment_method',
        'acr.validate_receipt','acr.voided_or_validated_by','acr.deposit_amount as total_deposit_amount',
        'ad.first_name','ad.last_name','ad.client_id as client_decode_id')
        ->where('acr.receipt_type',1);

		if ($request->has('client_id')) {
			$client_id 	= $request->input('client_id');
			if(trim($client_id) != ''){
				$query->where('ad.client_id', 'ilike', '%'.$client_id.'%');
			}
		}

		if ($request->has('name')) {
			$name =  $request->input('name');
			if(trim($name) != '') {
				$query->where(function ($q) use ($name) {
                    $q->where(DB::raw("COALESCE(ad.first_name, '') || ' ' || COALESCE(ad.last_name, '')"), 'ilike', "%{$name}%")
                      ->orWhere('ad.first_name', 'ilike', "%{$name}%")
                      ->orWhere('ad.last_name', 'ilike', "%{$name}%");
                });
			}
		}
      
        if ($request->has('trans_date')) {
			$trans_date =  $request->input('trans_date');
			if(trim($trans_date) != '') {
				$query->where('acr.trans_date', 'ilike', '%'.$trans_date.'%');
			}
		}

        if ($request->has('deposit_amount')) {
			$deposit_amount =  $request->input('deposit_amount');
			if(trim($deposit_amount) != '') {
				$query->where('acr.deposit_amount', '=', $deposit_amount);
			}
		}
      
        $query->orderBy('acr.id', 'desc');
        $totalData 	= $query->count();
        $lists = $query->paginate(20);
		return view('Admin.clients.clientreceiptlist', compact(['lists', 'totalData']));
    }

    /**
     * Validate receipt
     */
    public function validate_receipt(Request $request){
        $response = array();
        if( isset($request->clickedReceiptIds) && !empty($request->clickedReceiptIds) ){
            $affectedRows = DB::table('account_client_receipts')
            ->where('receipt_type', $request->receipt_type)
            ->whereIn('receipt_id', $request->clickedReceiptIds)
            ->update(['validate_receipt' => 1,'voided_or_validated_by' => Auth::user()->id]);
            if ($affectedRows > 0) {

                foreach($request->clickedReceiptIds as $ReceiptVal){
                    $receipt_info = AccountClientReceipt::select('user_id','client_id')->where('receipt_id', $ReceiptVal)->first();
                    $client_info = Admin::select('client_id')->where('id', $receipt_info->client_id)->first();

                    if($request->receipt_type == 1){
                        $subject = 'validated client receipt no -'.$ReceiptVal.' of client-'.$client_info->client_id;
                    } 
                    $objs = new ActivitiesLog;
                    $objs->client_id = $receipt_info->client_id;
                    $objs->created_by = Auth::user()->id;
                    $objs->description = '';
                    $objs->subject = $subject;
                    $objs->task_status = 0;
                    $objs->pin = 0;
                    $objs->save();
                }

                $record_data = DB::table('account_client_receipts')
                ->leftJoin('admins', 'admins.id', '=', 'account_client_receipts.voided_or_validated_by')
                ->select('account_client_receipts.id','account_client_receipts.voided_or_validated_by','admins.first_name','admins.last_name')
                ->where('account_client_receipts.receipt_type', $request->receipt_type)
                ->whereIn('account_client_receipts.receipt_id', $request->clickedReceiptIds)
                ->where('account_client_receipts.validate_receipt', 1)
                ->get();
                $response['record_data'] = 	$record_data;
                $response['status'] 	= 	true;
                $response['message']	=	'Record updated successfully.';
            } else {
                $response['status'] 	= 	true;
                $response['message']	=	'No record was updated.';
                $response['clickedIds'] = 	array();
            }
        }
        echo json_encode($response);
    }
  
    /**
     * Print preview
     */
    public function printpreview(Request $request, $id){
        $record_get = DB::table('account_client_receipts')->where('receipt_type',1)->where('id',$id)->get();
        if(!empty($record_get)){
            $clientname = DB::table('admins')->select('first_name','last_name','address','state','city','zip','country','dob')->where('id',$record_get[0]->client_id)->first();
            $profile = \App\Helpers\Helper::defaultCrmProfile();
            $admin = $profile ? (object)[
                'company_name' => $profile->company_name,
                'address' => $profile->address,
                'state' => '',
                'city' => '',
                'zip' => '',
                'email' => $profile->email,
                'phone' => $profile->phone,
                'dob' => null,
            ] : (object)['company_name'=>'','address'=>'','state'=>'','city'=>'','zip'=>'','email'=>'','phone'=>'','dob'=>null];
        }
        set_time_limit(3000);
        $pdf = PDF::setOptions([
			'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
			'logOutputFile' => storage_path('logs/log.htm'),
			'tempDir' => storage_path('logs/')
		])->loadView('emails.printpreview',compact(['record_get','clientname','admin']));
		return $pdf->stream('ClientReceipt.pdf');
	}
  
    /**
     * Get client receipt info by ID
     */
    public function getClientReceiptInfoById(Request $request)
	{
        $requestData = 	$request->all();
        $id = $requestData['id'];
        $record_get = DB::table('account_client_receipts')->where('receipt_type',1)->where('id',$id)->get();
        if(!empty($record_get)) {
            // Return data in consistent format with both property names for backward compatibility
            $response['record_get'] = $record_get;
            $response['requestData'] = $record_get; // Add this for consistency with frontend expectations
            $response['status'] 	= 	true;
            $response['message']	=	'Record is exist';
            $last_record_id = DB::table('account_client_receipts')->where('receipt_type',1)->max('id');
            $response['last_record_id'] = $last_record_id;
        }else{
            $response['record_get'] = array();
            $response['requestData'] = array(); // Add this for consistency
            $response['status'] 	= 	false;
            $response['message']	=	'Record is not exist.Please try again';
            $response['last_record_id'] = 0;
        }
        echo json_encode($response);
    }

    /**
     * Commission Report view
     */
    public function commissionreport() {
        return view('Admin.clients.commissionreport');
    }

    /**
     * Get commission report data
     */
    public function getcommissionreport(Request $request) {
        if ($request->ajax()) {
			$data = Application::join('admins', 'applications.client_id', '=', 'admins.id')
            ->leftJoin('partners', 'applications.partner_id', '=', 'partners.id')
            ->leftJoin('products', 'applications.product_id', '=', 'products.id')
            ->leftJoin('application_fee_options', 'applications.id', '=', 'application_fee_options.app_id')
            ->select('applications.*','admins.client_id as client_reference', 'admins.first_name','admins.last_name','admins.dob','partners.partner_name','products.name as coursename','application_fee_options.total_course_fee_amount','application_fee_options.enrolment_fee_amount','application_fee_options.material_fees','application_fee_options.tution_fees','application_fee_options.total_anticipated_fee','application_fee_options.fee_reported_by_college','application_fee_options.bonus_amount','application_fee_options.bonus_pending_amount','application_fee_options.bonus_paid','application_fee_options.commission_as_per_anticipated_fee','application_fee_options.commission_as_per_fee_reported','application_fee_options.commission_payable_as_per_anticipated_fee','application_fee_options.commission_paid_as_per_fee_reported','application_fee_options.commission_pending')
            ->where('applications.stage','Coe issued')
            ->orWhere('applications.stage','Enrolled')
            ->orWhere('applications.stage','Coe Cancelled')
            ->latest()->get();
            return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('client_reference', function($data) {
                if($data->client_reference){
                    $client_encoded_id = base64_encode(convert_uuencode(@$data->client_id)) ;
                    $client_reference = '<a href="'.route('clients.detail', $client_encoded_id).'" target="_blank" >'.$data->client_reference.'</a>';
                } else {
                    $client_reference = 'N/P';
                }
                return $client_reference;
            })
            ->addColumn('student_name', function($data) {
                if($data->first_name != ""){
                    $full_name = $data->first_name.' '.$data->last_name;
                } else {
                    $full_name = 'N/P';
                }
                return $full_name;
            })
            ->addColumn('dob', function($data) {
                if($data->dob != ""){
                    $dobArr = explode("-",$data->dob);
                    $dob = $dobArr[2]."/".$dobArr[1]."/".$dobArr[0];
                } else {
                    $dob = 'N/P';
                }
                return $dob;
            })
            ->addColumn('student_id', function($data) {
                if($data->student_id != ""){
                    $student_id = $data->student_id;
                } else {
                    $student_id = 'N/P';
                }
                return $student_id;
            })
            ->addColumn('college_name', function($data) {
                if($data->partner_name != ""){
                    $partner_name = $data->partner_name;
                } else {
                    $partner_name = 'N/P';
                }
                return $partner_name;
            })
            ->addColumn('course_name', function($data) {
                if($data->coursename != ""){
                    $coursename = $data->coursename;
                } else {
                    $coursename = 'N/P';
                }
                return $coursename;
            })
            ->addColumn('start_date', function($data) {
                if($data->start_date != ""){
                    $start_date = date('d/m/Y',strtotime($data->start_date));
                } else {
                    $start_date = 'N/P';
                }
                return $start_date;
            })
            ->addColumn('end_date', function($data) {
                if($data->end_date != ""){
                    $end_date = date('d/m/Y',strtotime($data->end_date));
                } else {
                    $end_date = 'N/P';
                }
                return $end_date;
            })
            ->addColumn('total_course_fee_amount', function($data) {
                if($data->total_course_fee_amount != ""){
                    $total_course_fee_amount = $data->total_course_fee_amount;
                } else {
                    $total_course_fee_amount = 'N/P';
                }
                return $total_course_fee_amount;
            })
            ->addColumn('enrolment_fee_amount', function($data) {
                if($data->enrolment_fee_amount != ""){
                    $enrolment_fee_amount = $data->enrolment_fee_amount;
                } else {
                    $enrolment_fee_amount = 'N/P';
                }
                return $enrolment_fee_amount;
            })
            ->addColumn('material_fees', function($data) {
                if($data->material_fees != ""){
                    $material_fees = $data->material_fees;
                } else {
                    $material_fees = 'N/P';
                }
                return $material_fees;
            })
            ->addColumn('tution_fees', function($data) {
                if($data->tution_fees != ""){
                    $tution_fees = $data->tution_fees;
                } else {
                    $tution_fees = 'N/P';
                }
                return $tution_fees;
            })
            ->addColumn('fee_reported_by_college', function($data) {
                if($data->fee_reported_by_college != ""){
                    $fee_reported_by_college = $data->fee_reported_by_college;
                } else {
                    $fee_reported_by_college = 'N/P';
                }
                return $fee_reported_by_college;
            })
            ->addColumn('bonus_amount', function($data) {
                if($data->bonus_amount != ""){
                    $bonus_amount = $data->bonus_amount;
                } else {
                    $bonus_amount = 'N/P';
                }
                return $bonus_amount;
            })
           ->addColumn('bonus_pending_amount', function($data) {
                if($data->bonus_pending_amount != ""){
                    $bonus_pending_amount = $data->bonus_pending_amount;
                } else {
                    $bonus_pending_amount = 'N/P';
                }
                return $bonus_pending_amount;
            })
            ->addColumn('bonus_paid', function($data) {
                if($data->bonus_paid != ""){
                    $bonus_paid = $data->bonus_paid;
                } else {
                    $bonus_paid = 'N/P';
                }
                return $bonus_paid;
            })
            ->addColumn('commission_as_per_fee_reported', function($data) {
                if($data->commission_as_per_fee_reported != ""){
                    $commission_as_per_fee_reported = $data->commission_as_per_fee_reported;
                } else {
                    $commission_as_per_fee_reported = 'N/P';
                }
                return $commission_as_per_fee_reported;
            })
            ->addColumn('commission_paid_as_per_fee_reported', function($data) {
                if($data->commission_paid_as_per_fee_reported != ""){
                    $commission_paid_as_per_fee_reported = $data->commission_paid_as_per_fee_reported;
                } else {
                    $commission_paid_as_per_fee_reported = 'N/P';
                }
                return $commission_paid_as_per_fee_reported;
            })
            ->addColumn('commission_pending', function($data) {
                if($data->commission_pending != ""){
                    $commission_pending = $data->commission_pending;
                } else {
                    $commission_pending = 'N/P';
                }
                return $commission_pending;
            })
            ->addColumn('student_status', function($data) {
                if($data->status == 0){
                    $student_status = "In Progress";
                } else if($data->status == 1){
                    $student_status = "Completed";
                } else if($data->status == 2){
                    $student_status = "Discontinued";
                } else if($data->status == 3){
                    $student_status = "Cancelled";
                }
                return $student_status;
            })
            ->rawColumns(['client_reference'])
            ->make(true);
        }
    }
}
