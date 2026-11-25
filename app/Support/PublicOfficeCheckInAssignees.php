<?php

namespace App\Support;

use App\Models\CheckinHistory;
use App\Models\Staff;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class PublicOfficeCheckInAssignees
{
    public static function historySubject(): string
    {
        return (string) config(
            'public_office_check_in.history_subject',
            'Check-in created via public office check-in page'
        );
    }

    /**
     * @return list<string>
     */
    public static function staffKeys(): array
    {
        $keys = config('public_office_check_in.assignee_staff_keys', []);
        if (! is_array($keys)) {
            return [];
        }

        $out = [];
        foreach ($keys as $key) {
            $key = strtolower(trim((string) $key));
            if ($key !== '' && ! in_array($key, $out, true)) {
                $out[] = $key;
            }
        }

        return $out;
    }

    /**
     * @return list<string>
     */
    public static function aliasesForKey(string $key): array
    {
        $key = strtolower(trim($key));
        $aliases = config('public_office_check_in.assignee_staff_aliases.'.$key, [$key]);
        if (! is_array($aliases)) {
            $aliases = [$key];
        }

        $out = [];
        foreach ($aliases as $alias) {
            $alias = strtolower(trim((string) $alias));
            if ($alias !== '' && ! in_array($alias, $out, true)) {
                $out[] = $alias;
            }
        }

        if ($out === []) {
            $out[] = $key;
        }

        return $out;
    }

    /**
     * Active staff matching the public check-in Set Assignee allowlist.
     *
     * @return Collection<int, Staff>
     */
    public static function resolveStaff(): Collection
    {
        if (! Schema::hasTable('staff')) {
            return collect();
        }

        $keys = self::staffKeys();
        if ($keys === []) {
            return collect();
        }

        $aliasSet = [];
        foreach ($keys as $key) {
            foreach (self::aliasesForKey($key) as $alias) {
                $aliasSet[$alias] = true;
            }
        }
        $aliases = array_keys($aliasSet);

        return Staff::query()
            ->active()
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get(['id', 'first_name', 'last_name', 'email', 'status'])
            ->filter(fn (Staff $staff): bool => self::staffMatchesAliases($staff, $aliases))
            ->values();
    }

    /**
     * @param  list<string>  $aliases
     */
    public static function staffMatchesAliases(Staff $staff, array $aliases): bool
    {
        $firstName = strtolower(trim((string) $staff->first_name));
        $emailLocal = strtolower(trim((string) explode('@', (string) $staff->email, 2)[0]));

        foreach ($aliases as $alias) {
            if ($firstName === $alias) {
                return true;
            }

            if ($firstName !== '' && str_starts_with($firstName, $alias.' ')) {
                return true;
            }

            if ($emailLocal === $alias || str_starts_with($emailLocal, $alias.'.')) {
                return true;
            }
        }

        return false;
    }

    public static function isAllowedStaffId(int $staffId): bool
    {
        if ($staffId <= 0) {
            return false;
        }

        return self::resolveStaff()->contains(fn (Staff $staff): bool => (int) $staff->id === $staffId);
    }

    /**
     * @param  list<int>  $checkinLogIds
     * @return list<int>
     */
    public static function publicCheckInLogIds(array $checkinLogIds): array
    {
        $wanted = [];
        foreach ($checkinLogIds as $id) {
            $id = (int) $id;
            if ($id > 0) {
                $wanted[$id] = true;
            }
        }

        if ($wanted === [] || ! Schema::hasTable('checkin_histories')) {
            return [];
        }

        return CheckinHistory::query()
            ->where('subject', self::historySubject())
            ->whereIn('checkin_id', array_keys($wanted))
            ->pluck('checkin_id')
            ->map(fn ($id): int => (int) $id)
            ->filter(fn (int $id): bool => isset($wanted[$id]))
            ->unique()
            ->values()
            ->all();
    }

    public static function isPublicCheckInLog(int $checkinLogId): bool
    {
        return in_array($checkinLogId, self::publicCheckInLogIds([$checkinLogId]), true);
    }
}
