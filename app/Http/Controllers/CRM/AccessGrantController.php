<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\ClientAccessGrant;
use App\Models\Staff;
use App\Services\CrmAccess\CrmAccessDeniedException;
use App\Services\CrmAccess\CrmAccessService;
use App\Support\StaffClientVisibility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccessGrantController extends Controller
{
    public function __construct(
        protected CrmAccessService $crmAccess
    ) {
        $this->middleware('auth:admin');
    }

    protected function ensureStaffMayOpenCrossAccessRequest(?Staff $user, int $adminId): void
    {
        if (! $user instanceof Staff || ! StaffClientVisibility::staffMayOpenCrossAccessRequest($user, $adminId)) {
            abort(403);
        }
    }

    /** JSON helper for access UI: clients module, approval queue users, or quick-access-only staff. */
    protected function ensureStaffForCrmAccessMeta(?Staff $user): void
    {
        if (! $user instanceof Staff) {
            abort(403);
        }
        if (StaffClientVisibility::staffHasClientsModule($user)) {
            return;
        }
        if ($this->crmAccess->isApprover($user)) {
            return;
        }
        if ((bool) ($user->quick_access_enabled ?? false)) {
            return;
        }
        abort(403);
    }

    /** Approvers, clients module, or quick-access users may view their grant history. */
    protected function ensureStaffClientsModuleOrApprover(): void
    {
        $user = Auth::guard('admin')->user();
        if (! $user instanceof Staff) {
            abort(403);
        }
        if ($this->crmAccess->isApprover($user)) {
            return;
        }
        if (StaffClientVisibility::staffHasClientsModule($user)) {
            return;
        }
        if ((bool) ($user->quick_access_enabled ?? false)) {
            return;
        }
        abort(403);
    }

    public function requestForm(Request $request, int $adminId)
    {
        /** @var Staff|null $user */
        $user = Auth::guard('admin')->user();
        $this->ensureStaffMayOpenCrossAccessRequest($user instanceof Staff ? $user : null, $adminId);
        /** @var Staff $user */
        $user = Auth::guard('admin')->user();
        $admin = Admin::query()
            ->where(function ($q) {
                $q->whereNull('is_deleted')->orWhere('is_deleted', 0);
            })
            ->findOrFail($adminId);

        if (StaffClientVisibility::canAccessAdminRecord($adminId, $user)) {
            $encodeId = base64_encode(convert_uuencode($admin->id));

            return $admin->type === 'lead'
                ? redirect()->to('/leads/detail/' . $encodeId)
                : redirect()->to('/clients/detail/' . $encodeId);
        }

        $offices = \App\Models\Branch::query()->orderBy('office_name')->get(['id', 'office_name']);
        $reasons = config('crm_access.quick_reason_options', []);
        $quickEnabled = (bool) ($user->quick_access_enabled ?? false);
        $canSupervisor = StaffClientVisibility::staffMayUseSupervisorAccessPath($user);

        return view('crm.access.request', compact('admin', 'offices', 'reasons', 'quickEnabled', 'canSupervisor'));
    }

    public function meta(Request $request)
    {
        /** @var Staff|null $user */
        $user = Auth::guard('admin')->user();
        $this->ensureStaffForCrmAccessMeta($user instanceof Staff ? $user : null);
        /** @var Staff $user */
        $user = Auth::guard('admin')->user();

        return response()->json([
            'offices' => \App\Models\Branch::query()->orderBy('office_name')->get(['id', 'office_name']),
            'reasons' => config('crm_access.quick_reason_options', []),
            'quick_access_enabled' => (bool) ($user->quick_access_enabled ?? false),
            'can_supervisor' => ! in_array((int) ($user->role ?? 0), config('crm_access.quick_access_only_role_ids', [9]), true),
            'is_approver' => $this->crmAccess->isApprover($user),
        ]);
    }

    public function queue(Request $request)
    {
        /** @var Staff $user */
        $user = Auth::guard('admin')->user();
        if (! $this->crmAccess->isApprover($user)) {
            abort(403);
        }

        $pending = ClientAccessGrant::query()
            ->with(['staff', 'admin'])
            ->where('status', 'pending')
            ->where('grant_type', 'supervisor_approved')
            ->orderByDesc('requested_at')
            ->paginate(25);

        return view('crm.access.queue', compact('pending'));
    }

    public function myGrants(Request $request)
    {
        $this->ensureStaffClientsModuleOrApprover();
        /** @var Staff $user */
        $user = Auth::guard('admin')->user();

        $grants = ClientAccessGrant::query()
            ->with(['admin'])
            ->where('staff_id', (int) $user->id)
            ->orderByDesc('requested_at')
            ->paginate(25);

        return view('crm.access.my-grants', compact('grants'));
    }

    public function quick(Request $request)
    {
        $request->validate([
            'admin_id' => 'required|integer|exists:admins,id',
            'office_id' => 'required|integer|exists:branches,id',
            'reason' => 'required|string|max:50',
        ]);

        /** @var Staff|null $user */
        $user = Auth::guard('admin')->user();
        if (! $user instanceof Staff) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized'], 403);
        }

        $adminId = (int) $request->input('admin_id');

        // Same as migrationmanager-style: already able to open record → go to detail (no grant needed)
        if (StaffClientVisibility::canAccessAdminRecord($adminId, $user)) {
            return response()->json([
                'ok' => true,
                'mode' => 'already_can_view',
                'grant_id' => null,
                'ends_at' => null,
            ]);
        }

        if (! StaffClientVisibility::staffMayOpenCrossAccessRequest($user, $adminId)) {
            return response()->json(['ok' => false, 'message' => 'You are not allowed to request cross-access for this record.'], 403);
        }

        $admin = Admin::query()
            ->where(function ($q) {
                $q->whereNull('is_deleted')->orWhere('is_deleted', 0);
            })
            ->findOrFail($adminId);
        $recordType = strtolower((string) ($admin->type ?? 'client')) === 'lead' ? 'lead' : 'client';

        try {
            $grant = $this->crmAccess->requestQuickGrant(
                $user,
                (int) $admin->id,
                $recordType,
                (int) $request->input('office_id'),
                (string) $request->input('reason')
            );
        } catch (CrmAccessDeniedException $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json([
            'ok' => true,
            'mode' => 'new_grant',
            'grant_id' => $grant->id,
            'ends_at' => $grant->ends_at?->toIso8601String(),
        ]);
    }

    public function supervisor(Request $request)
    {
        $request->validate([
            'admin_id' => 'required|integer|exists:admins,id',
            'office_id' => 'required|integer|exists:branches,id',
            'reason' => 'required|string|max:50',
            'note' => 'nullable|string|max:2000',
        ]);

        /** @var Staff|null $user */
        $user = Auth::guard('admin')->user();
        $this->ensureStaffMayOpenCrossAccessRequest($user instanceof Staff ? $user : null, (int) $request->input('admin_id'));
        /** @var Staff $user */
        $user = Auth::guard('admin')->user();
        if (! StaffClientVisibility::staffMayUseSupervisorAccessPath($user)) {
            return response()->json(['ok' => false, 'message' => 'Supervisor access requests are not enabled for your account.'], 403);
        }
        $admin = Admin::query()
            ->where(function ($q) {
                $q->whereNull('is_deleted')->orWhere('is_deleted', 0);
            })
            ->findOrFail((int) $request->input('admin_id'));
        $recordType = strtolower((string) ($admin->type ?? 'client')) === 'lead' ? 'lead' : 'client';

        try {
            $grant = $this->crmAccess->requestSupervisorGrant(
                $user,
                (int) $admin->id,
                $recordType,
                (int) $request->input('office_id'),
                (string) $request->input('reason'),
                (string) $request->input('note', '')
            );
        } catch (CrmAccessDeniedException $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json(['ok' => true, 'grant_id' => $grant->id]);
    }

    public function approve(Request $request, int $id)
    {
        /** @var Staff $user */
        $user = Auth::guard('admin')->user();
        try {
            $this->crmAccess->approveGrant($user, $id);
        } catch (CrmAccessDeniedException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Access request approved.');
    }

    public function reject(Request $request, int $id)
    {
        $request->validate(['reason' => 'nullable|string|max:2000']);

        /** @var Staff $user */
        $user = Auth::guard('admin')->user();
        try {
            $this->crmAccess->rejectGrant($user, $id, (string) $request->input('reason', ''));
        } catch (CrmAccessDeniedException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Access request rejected.');
    }
}
