<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * Resolves staff names for document UI without Eloquent in large controllers
 * (avoids static analyzers choking on App\Models\Staff in mixed PHP/HTML).
 *
 * Mirrors: Staff::… / Staff::… ?? Admin::… on the staff and admins tables.
 */
final class ClientDocumentStaffResolver
{
    /**
     * Same idea as Staff::query()->select('first_name')->find($id).
     */
    public static function firstNameRowByStaffId(mixed $id): ?object
    {
        if ($id === null || $id === '') {
            return null;
        }

        return DB::table('staff')
            ->select('first_name')
            ->where('id', $id)
            ->first();
    }

    /**
     * Staff first_name row, or admins first_name for the same id (legacy behavior).
     */
    public static function firstNameRowStaffThenAdmin(mixed $id): ?object
    {
        if ($id === null || $id === '') {
            return null;
        }

        $staff = self::firstNameRowByStaffId($id);
        if ($staff) {
            return $staff;
        }

        return DB::table('admins')
            ->select('first_name')
            ->where('id', $id)
            ->first();
    }

    /**
     * Same idea as Staff::find($id) for read-only use (first_name, etc.).
     */
    public static function staffRowById(mixed $id): ?object
    {
        if ($id === null || $id === '') {
            return null;
        }

        return DB::table('staff')->where('id', $id)->first();
    }
}
