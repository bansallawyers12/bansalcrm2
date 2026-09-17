<?php

namespace App\Support;

use App\Models\Staff;
use Illuminate\Database\Eloquent\Collection;

/**
 * Resolves a Staff row from assignee-style fields that may store a single id or comma-separated ids.
 */
final class StaffAssigneeResolver
{
    /**
     * Use the first numeric segment when comma-separated so PostgreSQL never receives an invalid bigint string (e.g. "1,1215").
     */
    public static function firstStaffFromAssigneeValue(mixed $value): ?Staff
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        if (str_contains($value, ',')) {
            $parts = explode(',', $value);
            $firstId = trim((string) ($parts[0] ?? ''));
            if ($firstId !== '' && is_numeric($firstId)) {
                return Staff::query()->find((int) $firstId);
            }

            return null;
        }

        if (is_numeric($value)) {
            return Staff::query()->find((int) $value);
        }

        return null;
    }

    /**
     * Positive integer staff ids from a single or comma-separated assignee value.
     * Empty, whitespace, and non-numeric segments are skipped so PostgreSQL never binds ''.
     *
     * @return list<int>
     */
    public static function numericIdsFromAssigneeValue(mixed $value): array
    {
        if ($value === null || $value === '') {
            return [];
        }

        $value = trim((string) $value);
        if ($value === '') {
            return [];
        }

        $parts = str_contains($value, ',') ? explode(',', $value) : [$value];
        $ids = [];

        foreach ($parts as $part) {
            $part = trim((string) $part);
            if ($part === '' || ! ctype_digit($part)) {
                continue;
            }

            $id = (int) $part;
            if ($id < 1) {
                continue;
            }

            $ids[] = $id;
        }

        return array_values(array_unique($ids));
    }

    /**
     * All matching staff for the assignee field. Empty assignee yields no query.
     *
     * @return Collection<int, Staff>
     */
    public static function staffCollectionFromAssigneeValue(mixed $value): Collection
    {
        $ids = self::numericIdsFromAssigneeValue($value);
        if ($ids === []) {
            return new Collection;
        }

        return Staff::query()
            ->select(['id', 'first_name', 'last_name'])
            ->whereIn('id', $ids)
            ->get();
    }
}
