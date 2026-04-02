<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

use App\Models\Staff;
use App\Models\FromEmail;
use App\Models\StaffRole;

use Auth;
use Config;
use App\Helpers\PhoneHelper;
use App\Services\CrmAccess\CrmAccessService;

class StaffController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function create(Request $request)
    {
        $check = $this->checkAuthorizationAction('user_management', $request->route()->getActionMethod(), Auth::user()->role);
        if ($check) {
            return redirect()->route('dashboard')->with('error', config('constants.unauthorized'));
        }

        // Staff roles only (exclude role 7 = client)
        $usertype = StaffRole::where('id', '!=', 7)->get();
$emails = FromEmail::where('status', 1)->orderBy('email')->get();
        $canManageCrmAccess = app(CrmAccessService::class)->canManageStaffQuickAccess(Auth::user());
		return view('Admin.staff.create', compact(['usertype', 'emails', 'canManageCrmAccess']));
    }

    public function store(Request $request)
    {
        $check = $this->checkAuthorizationAction('user_management', $request->route()->getActionMethod(), Auth::user()->role);
        if ($check) {
            return redirect()->route('dashboard')->with('error', config('constants.unauthorized'));
        }

        if ($request->isMethod('post')) {
            $requestData = $request->all();
            $this->validate($request, [
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'email' => 'required|max:255|unique:staff',
                'password' => 'required|max:255|confirmed',
                'phone' => 'required',
                'role' => 'required',
                'office' => 'nullable|exists:branches,id',
            ]);

            $obj = new Staff;
            $obj->first_name = @$requestData['first_name'];
            $obj->last_name = @$requestData['last_name'];
            $obj->email = @$requestData['email'];
            $obj->country_code = PhoneHelper::normalizeCountryCode(@$requestData['country_code']);
            $obj->position = @$requestData['position'];
            $obj->password = Hash::make(@$requestData['password']);
            $obj->phone = @$requestData['phone'];
            $obj->role = @$requestData['role'];
            $obj->office_id = @$requestData['office'];
            $obj->team = @$requestData['team'];
            $obj->verified = 1;
            $obj->status = 1;
            $obj->email_signature = $request->input('email_signature');
            if (isset($requestData['show_dashboard_per'])) {
                $obj->show_dashboard_per = 1;
            } else {
                $obj->show_dashboard_per = 0;
            }
            if (isset($requestData['permission']) && is_array($requestData['permission'])) {
                $obj->permission = implode(',', $requestData['permission']);
            } else {
                $obj->permission = '';
            }

            if (app(CrmAccessService::class)->canManageStaffQuickAccess(Auth::user())) {
                $obj->quick_access_enabled = $request->boolean('quick_access_enabled');
                $obj->crm_full_access = $request->boolean('crm_full_access');
                $obj->crm_access_approver = $request->boolean('crm_access_approver');
            }

            $saved = $obj->save();

            if (!$saved) {
                return redirect()->back()->with('error', Config::get('constants.server_error'));
            }
            return redirect()->route('staff.active')->with('success', 'Staff added Successfully');
        }

        return view('Admin.staff.create');
    }

    public function edit(Request $request, $id = null)
    {
        $check = $this->checkAuthorizationAction('user_management', $request->route()->getActionMethod(), Auth::user()->role);
        if ($check) {
            return redirect()->route('dashboard')->with('error', config('constants.unauthorized'));
        }

        $usertype = StaffRole::where('id', '!=', 7)->get();
        if ($request->isMethod('post')) {
            $requestData = $request->all();

            $this->validate($request, [
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'email' => 'required|email|max:255|unique:staff,email,' . (@$requestData['id'] ?? 0),
                'phone' => 'required|max:255',
                'office' => 'nullable|exists:branches,id',
            ]);

            $obj = Staff::find(@$requestData['id']);
            $obj->first_name = @$requestData['first_name'];
            $obj->last_name = @$requestData['last_name'];
            $obj->email = @$requestData['email'];
            $obj->country_code = PhoneHelper::normalizeCountryCode(@$requestData['country_code']);
            $obj->position = @$requestData['position'];
            $obj->phone = @$requestData['phone'];
            $obj->role = @$requestData['role'];
            $obj->office_id = @$requestData['office'];
            $obj->team = @$requestData['team'];

            if (isset($requestData['permission']) && $requestData['permission'] != '') {
                $obj->permission = implode(',', $requestData['permission']);
            } else {
                $obj->permission = '';
            }

            if (isset($requestData['show_dashboard_per'])) {
                $obj->show_dashboard_per = 1;
            } else {
                $obj->show_dashboard_per = 0;
            }
            $obj->email_signature = $request->input('email_signature');
            if (!empty(@$requestData['password'])) {
                $obj->password = Hash::make(@$requestData['password']);
            }
            $obj->phone = @$requestData['phone'];

            $crmAccess = app(CrmAccessService::class);
            if ($crmAccess->canManageStaffQuickAccess(Auth::user())) {
                $wasQuick = (bool) ($obj->quick_access_enabled ?? false);
                $obj->quick_access_enabled = $request->boolean('quick_access_enabled');
                $obj->crm_full_access = $request->boolean('crm_full_access');
                $obj->crm_access_approver = $request->boolean('crm_access_approver');
                if ($wasQuick && ! $obj->quick_access_enabled) {
                    $crmAccess->revokeGrantsForStaff((int) $obj->id, 'Quick access disabled by admin');
                }
            }

            $saved = $obj->save();

            if (!$saved) {
                return redirect()->back()->with('error', Config::get('constants.server_error'));
            }
            return redirect()->route('staff.view', ['id' => @$requestData['id']])->with('success', 'Staff Edited Successfully');
        }

        if (isset($id) && !empty($id)) {
            $id = $this->decodeString($id);
            if ($id === false || $id === '') {
                return redirect()->route('staff.active')->with('error', 'Invalid Staff ID');
            }
            if (Staff::where('id', '=', $id)->exists()) {
                $fetchedData = Staff::with(['office'])->find($id);
$emails = FromEmail::where('status', 1)->orderBy('email')->get();
                $canManageCrmAccess = app(CrmAccessService::class)->canManageStaffQuickAccess(Auth::user());
				return view('Admin.staff.edit', compact(['fetchedData', 'usertype', 'emails', 'canManageCrmAccess']));
            }
            return redirect()->route('staff.active')->with('error', 'Staff Not Exist');
        }
        return redirect()->route('staff.active')->with('error', Config::get('constants.unauthorized'));
    }

    public function savezone(Request $request)
    {
        if ($request->isMethod('post')) {
            $requestData = $request->all();
            $obj = Staff::find(@$requestData['user_id']);
            $obj->time_zone = @$requestData['timezone'];
            $saved = $obj->save();

            if (!$saved) {
                return redirect()->back()->with('error', Config::get('constants.server_error'));
            }
            return redirect()->route('staff.view', ['id' => @$requestData['user_id']])->with('success', 'Staff Edited Successfully');
        }
    }

    public function view(Request $request, $id)
    {
        if (isset($id) && !empty($id)) {
            if (Staff::where('id', '=', $id)->exists()) {
                $fetchedData = Staff::with('office')->find($id);
                return view('Admin.staff.view', compact(['fetchedData']));
            }
            return redirect()->route('staff.active')->with('error', 'Staff Not Exist');
        }
    }

    public function active(Request $request)
    {
        $req_data = $request->all();
        $search_by = $req_data['search_by'] ?? '';

        if ($search_by) {
            $query = Staff::where('status', '=', 1)
                ->where(function ($q) use ($search_by) {
                    $q->where('first_name', 'LIKE', '%' . $search_by . '%')
                        ->orWhere('last_name', 'LIKE', '%' . $search_by . '%');
                })->with(['usertype', 'office']);
        } else {
            $query = Staff::where('status', '=', 1)->with(['usertype', 'office']);
        }

        $totalData = $query->count();
        $lists = $query->orderby('first_name', 'ASC')->paginate(config('constants.limit'));
        $viewType = 'active';
        return view('Admin.staff.index', compact(['lists', 'totalData', 'viewType']));
    }

    public function inactive(Request $request)
    {
        $req_data = $request->all();
        $search_by = $req_data['search_by'] ?? '';

        if ($search_by) {
            $query = Staff::where('status', '=', 0)
                ->where(function ($q) use ($search_by) {
                    $q->where('first_name', 'LIKE', '%' . $search_by . '%')
                        ->orWhere('last_name', 'LIKE', '%' . $search_by . '%');
                })->with(['usertype', 'office']);
        } else {
            $query = Staff::where('status', '=', 0)->with(['usertype', 'office']);
        }

        $totalData = $query->count();
        $lists = $query->orderby('first_name', 'ASC')->paginate(config('constants.limit'));
        $viewType = 'inactive';
        return view('Admin.staff.index', compact(['lists', 'totalData', 'viewType']));
    }

    /**
     * AJAX: Get staff for assignee dropdown (search by name, email, phone).
     */
    public function getassigneeajax(Request $request)
    {
        $squery = $request->likevalue ?? '';
        $fetchedData = Staff::where(function ($query) use ($squery) {
            return $query
                ->where('email', 'ILIKE', '%' . $squery . '%')
                ->orWhere('first_name', 'ILIKE', '%' . $squery . '%')
                ->orWhere('last_name', 'ILIKE', '%' . $squery . '%')
                ->orWhere('phone', 'ILIKE', '%' . $squery . '%')
                ->orWhere(DB::raw("COALESCE(first_name, '') || ' ' || COALESCE(last_name, '')"), 'ILIKE', '%' . $squery . '%');
        })->get();

        $agents = [];
        foreach ($fetchedData as $list) {
            $agents[] = [
                'id' => $list->id,
                'agent_id' => $list->first_name . ' ' . $list->last_name,
                'assignee' => $list->first_name . ' ' . $list->last_name,
            ];
        }
        echo json_encode($agents);
    }

    /**
     * Get assignee list for Action module dropdown.
     */
    public function getAssigneeList(Request $request)
    {
        $assignedto = $request->assignedto ?? null;
        $content1 = [];
        foreach (Staff::with('office')->where('status', 1)->orderby('first_name', 'ASC')->get() as $staff) {
            $officeName = $staff->office ? $staff->office->office_name : '';
            $option_value = $staff->first_name . ' ' . $staff->last_name . ' (' . $officeName . ')';

            if ($staff->id == $assignedto) {
                $content1[] = '<option value="' . $staff->id . '" selected>' . $option_value . '</option>';
            } else {
                $content1[] = '<option value="' . $staff->id . '">' . $option_value . '</option>';
            }
        }
        $response['status'] = true;
        $response['message'] = $content1;
        echo json_encode($response);
    }
}
