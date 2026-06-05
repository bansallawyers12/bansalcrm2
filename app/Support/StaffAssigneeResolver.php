<?php

namespace App\Support;

use App\Models\Staff;

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
}
