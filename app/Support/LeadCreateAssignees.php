<?php

namespace App\Support;

use App\Models\Staff;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class LeadCreateAssignees
{
    /**
     * Staff IDs allowed in the lead create "Assign To" dropdown.
     *
     * @return list<int>
     */
    public static function allowedStaffIds(): array
    {
        $ids = config('crm.lead_create_assignee_ids', []);

        if (! is_array($ids)) {
            return [];
        }

        return array_values(array_filter(array_map(
            static fn (mixed $id): int => (int) $id,
            $ids
        ), static fn (int $id): bool => $id > 0));
    }

    /**
     * Active staff rows for the lead create assignee dropdown.
     *
     * @return Collection<int, Staff>
     */
    public static function assignableStaff(): Collection
    {
        $ids = static::allowedStaffIds();

        if ($ids === []) {
            return new Collection;
        }

        return static::staffQueryForIds($ids)
            ->where('status', 1)
            ->get();
    }

    /**
     * First client assignee when it is allowed for the Add Application dropdown.
     */
    public static function preferredApplicationAssigneeIdFromClientAssignee(mixed $assigneeValue): ?int
    {
        $ids = StaffAssigneeResolver::numericIdsFromAssigneeValue($assigneeValue);
        if ($ids === []) {
            return null;
        }

        $primaryId = (int) $ids[0];
        $allowedIds = static::allowedStaffIds();

        return in_array($primaryId, $allowedIds, true) ? $primaryId : null;
    }

    /**
     * Staff IDs accepted on client/lead edit save: allowed list plus existing assignees.
     *
     * @param  list<int>  $currentAssigneeIds
     * @return list<int>
     */
    public static function permittedStaffIdsForEdit(array $currentAssigneeIds): array
    {
        $normalizedCurrentIds = array_values(array_filter(array_map(
            static fn (mixed $id): int => (int) $id,
            $currentAssigneeIds
        ), static fn (int $id): bool => $id > 0));

        return array_values(array_unique(array_merge(
            static::allowedStaffIds(),
            $normalizedCurrentIds
        )));
    }

    /**
     * Allowed staff for edit dropdown: configured list plus any already-assigned staff.
     *
     * @param  list<int>  $currentAssigneeIds
     * @return Collection<int, Staff>
     */
    public static function assignableStaffForEdit(array $currentAssigneeIds): Collection
    {
        $allowedIds = static::allowedStaffIds();
        $currentIds = array_values(array_filter(array_map(
            static fn (mixed $id): int => (int) $id,
            $currentAssigneeIds
        ), static fn (int $id): bool => $id > 0));

        $ids = static::permittedStaffIdsForEdit($currentIds);

        if ($ids === []) {
            return new Collection;
        }

        $grandfatherIds = array_values(array_diff($currentIds, $allowedIds));

        return static::staffQueryForIds($ids)
            ->where(function (Builder $query) use ($grandfatherIds): void {
                $query->where('status', 1);

                if ($grandfatherIds !== []) {
                    $query->orWhereIn('id', $grandfatherIds);
                }
            })
            ->get();
    }

    /**
     * @param  list<int>  $ids
     * @return Builder<Staff>
     */
    private static function staffQueryForIds(array $ids): Builder
    {
        return Staff::query()
            ->whereIn('id', $ids)
            ->orderBy('first_name')
            ->with('office:id,office_name')
            ->select(['id', 'first_name', 'last_name', 'office_id', 'status']);
    }
}
