<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Invoice;
use App\ShareInvoice;
use Cookie;
use Config;
use Auth;
use PDF;

class InvoiceController extends Controller
{
	public function __construct(Request $request)
    {
	}
	
	public function customer_invoice_download($id)
    {
		if(isset($id) && !empty($id)) 
			{
				$id = $this->decodeString($id);	
				$shareinvoice = ShareInvoice::where('invoice_link',$id)->where('status',1)->whereDate('expire_date','>', date('Y-m-d'))->with(['company'])->first();
				if($shareinvoice) 
				{
					
					$invoicedetail = Invoice::where('id',$shareinvoice->invoice_id)->with(['customer','invoicedetail'])->first();
					$invoicefilename = $invoicedetail->invoice.'-'.$invoicedetail->id.'.pdf';
					$pdf = PDF::setOptions([
                        'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
                        'logOutputFile' => storage_path('logs/log.htm'),
                        'tempDir' => storage_path('logs/')
                    ])->loadView('invoices.invoice', compact('invoicedetail'));
					return $pdf->download($invoicefilename); 
				}else{
					abort(404);
				}
		}else{ abort(404); }
	}
	
	public function customer_invoice_print($id)
    {
       if(isset($id) && !empty($id)) 
			{
				$id = $this->decodeString($id);	
				$shareinvoice = ShareInvoice::where('invoice_link',$id)->where('status',1)->whereDate('expire_date','>', date('Y-m-d'))->with(['company'])->first();
				if($shareinvoice) 
				{
					
					$invoicedetail = Invoice::where('id',$shareinvoice->invoice_id)->with(['customer','invoicedetail'])->first();
					$invoicefilename = $invoicedetail->invoice.'-'.$invoicedetail->id.'.pdf';
					$pdf = PDF::setOptions([
                        'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
                        'logOutputFile' => storage_path('logs/log.htm'),
                        'tempDir' => storage_path('logs/')
                    ])->loadView('invoices.invoice', compact('invoicedetail'));
					return $pdf->stream($invoicefilename); 
				}else{
					abort(404);
				}
		}else{ abort(404); }
    }

	public function invoice(Request $request, $slug = null)
	{
		if(isset($slug) && !empty($slug)) 
		{
			$id = $this->decodeString($slug);	
		//DB::enableQueryLog(); 
			$shareinvoice = ShareInvoice::where('invoice_link',$id)->where('status',1)->whereDate('expire_date','>', date('Y-m-d'))->with(['company'])->first();
			//dd(DB::getQueryLog());
			if($shareinvoice){
				$invoicedetail = Invoice::where('id',$shareinvoice->invoice_id)->with(['customer','invoicedetail'])->first();
				if($invoicedetail){
					return view('Frontend.invoice.index', compact(['shareinvoice','invoicedetail']));
				}else{
					abort(404);
				}
			}else{
				$shareinvoice = ShareInvoice::where('invoice_link',$id)->with(['company'])->first();
				if($shareinvoice){
				return view('Frontend.invoice.notfound', compact(['shareinvoice']));
				}else{
					abort(404);
				}
			}
		}	
	}	
}
?>	