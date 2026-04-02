<?php

namespace App\Support;

use App\Models\Admin;
use App\Models\Staff;
use App\Services\CrmAccess\CrmAccessService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class StaffClientVisibility
{
    public static function strictAllocationEnabled(): bool
    {
        return (bool) config('crm_access.strict_allocation', true);
    }

    public static function isExemptFromAllocation(Authenticatable $user): bool
    {
        if (! $user instanceof Staff) {
            return false;
        }

        return app(CrmAccessService::class)->isExemptFromAllocation($user);
    }

    /**
     * Whether the logged-in staff may open this admins row (by admins.id).
     */
    public static function canAccessAdminRecord(int $adminId, ?Authenticatable $user = null): bool
    {
        $user = $user ?? Auth::guard('admin')->user();

        if (! $user instanceof Staff) {
            return false;
        }

        $row = Admin::query()
            ->where('id', $adminId)
            ->whereNull('is_deleted')
            ->first(['id', 'type', 'user_id', 'assignee']);

        if (! $row) {
            return false;
        }

        if (! self::strictAllocationEnabled()) {
            return true;
        }

        if (self::isExemptFromAllocation($user)) {
            return true;
        }

        $svc = app(CrmAccessService::class);
        if ($svc->hasActiveGrant($user, $adminId)) {
            return true;
        }

        return self::userAllocatedToRecord($user, $row);
    }

    public static function userAllocatedToRecord(Staff $user, Admin $row): bool
    {
        $sid = (int) $user->id;

        if ((int) ($row->user_id ?? 0) === $sid) {
            return true;
        }

        $assignee = trim((string) ($row->assignee ?? ''));
        if ($assignee === '') {
            return false;
        }

        if ((string) (int) $assignee === $assignee && (int) $assignee === $sid) {
            return true;
        }

        $parts = array_filter(array_map('trim', explode(',', $assignee)));

        return in_array((string) $sid, $parts, true) || in_array($sid, array_map('intval', $parts), true);
    }

    /**
     * Restrict admins query to rows the current staff may see (allocation + active grants).
     */
    public static function restrictAdminsQueryForStaff(Builder $query, ?Authenticatable $user = null): Builder
    {
        $user = $user ?? Auth::guard('admin')->user();

        if (! $user instanceof Staff) {
            return $query->whereRaw('1 = 0');
        }

        if (! self::strictAllocationEnabled()) {
            return $query;
        }

        if (self::isExemptFromAllocation($user)) {
            return $query;
        }

        $staffId = (int) $user->id;
        $now = now('UTC')->format('Y-m-d H:i:s');

        return $query->where(function (Builder $outer) use ($staffId, $now) {
            $outer->where('user_id', $staffId)
                ->orWhere('assignee', (string) $staffId)
                ->orWhere('assignee', 'like', $staffId . ',%')
                ->orWhere('assignee', 'like', '%,' . $staffId . ',%')
                ->orWhere('assignee', 'like', '%,' . $staffId)
                ->orWhereExists(function ($sub) use ($staffId, $now) {
                    $sub->selectRaw('1')
                        ->from('client_access_grants as cag')
                        ->whereColumn('cag.admin_id', 'admins.id')
                        ->where('cag.staff_id', $staffId)
                        ->where('cag.status', 'active')
                        ->whereNotNull('cag.ends_at')
                        ->where('cag.ends_at', '>', $now);
                });
        });
    }
}
