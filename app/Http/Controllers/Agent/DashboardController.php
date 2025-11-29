<?php
namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema; 
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Redirect;

use App\Agent;
use Auth;
use Config;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:agents');
    }
    
     public function dashboard()
     {
         return view('Agent.dashboard');
     }
     
     public function getpartner(Request $request){
		$catid = $request->cat_id;
		$lists = \App\Partner::where('service_workflow', $catid)->orderby('partner_name','ASC')->get();
		ob_start();
		?>
		<option value="">Select a Partner</option>
		<?php
		foreach($lists as $list){
			?>
			<option value="<?php echo $list->id; ?>"><?php echo $list->partner_name; ?></option>
			<?php
		}
		echo ob_get_clean();
	}
	
	public function getproduct(Request $request){
		$catid = $request->cat_id;
		$lists = \App\Product::where('partner', $catid)->orderby('name','ASC')->get();
		ob_start();
		?>
		<option value="">Select a Product</option>
		<?php
		foreach($lists as $list){
			?>
			<option value="<?php echo $list->id; ?>"><?php echo $list->name; ?></option>
			<?php
		}
		echo ob_get_clean();
	}
		public function getbranch(Request $request){
		$catid = $request->cat_id;
		$pro = \App\Product::where('id', $catid)->first();
		if($pro){
		$user_array = explode(',',$pro->branches);
		$lists = \App\PartnerBranch::WhereIn('id',$user_array)->Where('partner_id',$pro->partner)->orderby('name','ASC')->get();
		ob_start();
		?>
		<option value="">Select a Branch</option>
		<?php
		foreach($lists as $list){
			?>
			<option value="<?php echo $list->id; ?>"><?php echo $list->name; ?></option>
			<?php
		}
		}else{
			?>
			<option value="">Select a Branch</option>
			<?php
		}
		echo ob_get_clean();
	}
}
?>