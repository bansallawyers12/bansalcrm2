<?php

namespace App\Support;

use App\Http\Controllers\Admin\FollowupController;
use App\Models\FollowupCalendarBlockTiming;
use App\Models\FollowupCalendarSetting;
use App\Models\FollowupConsultant;
use App\Models\Note;
use Carbon\Carbon;

/**
 * Computes bookable slot starts per consultant using followup_calendar_settings,
 * active followup_calendar_block_timings (consultant_slugs must include the slug),
 * and existing open Followup notes for that consultant on the requested date.
 */
final class FollowupAvailability
{
    /**
     * Active calendar row for this consultant + service, or null.
     */
    protected static function resolveActiveSetting(string $consultantSlug, string $serviceType = 'free'): ?FollowupCalendarSetting
    {
        $consultant = FollowupConsultant::query()
            ->where('slug', $consultantSlug)
            ->where('status', true)
            ->first();

        if (! $consultant) {
            return null;
        }

        return FollowupCalendarSetting::query()
            ->where('followup_consultant_id', $consultant->id)
            ->where('service_type', $serviceType)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Flatpickr uses JavaScript getDay(): 0 = Sunday … 6 = Saturday.
     * Settings use PHP format('N'): 1 = Monday … 7 = Sunday.
     *
     * @return list<int> Weekday indexes to disable in Flatpickr (empty = allow all days).
     */
    public static function disabledJsWeekdays(string $consultantSlug, string $serviceType = 'free'): array
    {
        $setting = self::resolveActiveSetting($consultantSlug, $serviceType);
        if (! $setting) {
            return [];
        }

        $days = $setting->available_days;
        if (! is_array($days) || count($days) === 0) {
            return [];
        }

        $allowedPhp = array_map('intval', $days);
        $allowedJs = [];
        foreach ($allowedPhp as $n) {
            $allowedJs[] = $n === 7 ? 0 : $n;
        }

        $disabled = [];
        for ($js = 0; $js <= 6; $js++) {
            if (! in_array($js, $allowedJs, true)) {
                $disabled[] = $js;
            }
        }

        return $disabled;
    }

    public static function slotDurationMinutes(string $consultantSlug, string $serviceType = 'free'): ?int
    {
        $setting = self::resolveActiveSetting($consultantSlug, $serviceType);

        return $setting ? max(5, (int) $setting->slot_duration_minutes) : null;
    }

    /**
     * @return list<string> Start times as H:i (24h)
     */
    public static function slotStartsFor(string $consultantSlug, string $dateYmd, string $serviceType = 'free', ?int $excludeNoteId = null): array
    {
        $setting = self::resolveActiveSetting($consultantSlug, $serviceType);

        if (! $setting) {
            return [];
        }

        $day = (int) Carbon::parse($dateYmd)->format('N');
        $days = $setting->available_days;
        if (is_array($days) && count($days) > 0) {
            $daysInt = array_map('intval', $days);
            if (! in_array($day, $daysInt, true)) {
                return [];
            }
        }

        $slotMinutes = max(5, (int) $setting->slot_duration_minutes); // same floor as slotDurationMinutes()
        $start = Carbon::parse($dateYmd.' '.$setting->start_time);
        $end = Carbon::parse($dateYmd.' '.$setting->end_time);

        if ($end->lte($start)) {
            return [];
        }

        $slots = [];
        $cursor = $start->copy();
        while ($cursor->copy()->addMinutes($slotMinutes)->lte($end)) {
            $slots[] = $cursor->format('H:i');
            $cursor->addMinutes($slotMinutes);
        }

        $dateCarbon = Carbon::parse($dateYmd)->startOfDay();
        $blocked = self::blockedIntervalsForConsultantDate($consultantSlug, $dateCarbon);
        $booked = self::bookedIntervalsForConsultantDate($consultantSlug, $dateYmd, $serviceType, $excludeNoteId);
        $busy = array_merge($blocked, $booked);

        return array_values(array_filter($slots, function ($hm) use ($dateYmd, $slotMinutes, $busy) {
            $slotStart = Carbon::parse($dateYmd.' '.$hm.':00');
            $slotEnd = $slotStart->copy()->addMinutes($slotMinutes);

            foreach ($busy as [$bStart, $bEnd]) {
                if ($slotStart->lt($bEnd) && $slotEnd->gt($bStart)) {
                    return false;
                }
            }

            return true;
        }));
    }

    public static function isValidSlotSelection(string $consultantSlug, string $dateYmd, string $slotHm, string $serviceType = 'free', ?int $excludeNoteId = null): bool
    {
        return in_array($slotHm, self::slotStartsFor($consultantSlug, $dateYmd, $serviceType, $excludeNoteId), true);
    }

    /**
     * Open follow-up notes already scheduled for this consultant on this calendar date (same overlap rules as blocks).
     *
     * @return array<int, array{0: Carbon, 1: Carbon}>
     */
    protected static function bookedIntervalsForConsultantDate(string $consultantSlug, string $dateYmd, string $serviceType, ?int $excludeNoteId): array
    {
        $slotMinutes = self::slotDurationMinutes($consultantSlug, $serviceType);
        if ($slotMinutes === null) {
            return [];
        }

        $titles = FollowupController::followupNoteTitlesForCalendar($consultantSlug);
        if (count($titles) === 0) {
            return [];
        }

        $q = Note::query()
            ->where('type', 'client')
            ->where('task_group', 'Followup')
            ->where('is_action', 1)
            ->where('status', 0)
            ->whereIn('title', $titles)
            ->whereDate('action_assign_date', $dateYmd);

        if ($excludeNoteId !== null) {
            $q->where('id', '!=', $excludeNoteId);
        }

        $intervals = [];
        foreach ($q->get(['id', 'action_assign_date']) as $note) {
            $slotStart = Carbon::parse($note->action_assign_date);
            $intervals[] = [$slotStart, $slotStart->copy()->addMinutes($slotMinutes)];
        }

        return $intervals;
    }

    protected static function blockedIntervalsForConsultantDate(string $consultantSlug, Carbon $date): array
    {
        $blocks = FollowupCalendarBlockTiming::query()
            ->where('is_active', true)
            ->get();

        $intervals = [];
        foreach ($blocks as $block) {
            $slugs = $block->consultant_slugs ?? [];
            if (! is_array($slugs)) {
                $slugs = [];
            }
            if (count($slugs) > 0 && ! in_array($consultantSlug, $slugs, true)) {
                continue;
            }

            if (! self::blockAppliesToDate($block, $date)) {
                continue;
            }

            if ($block->is_all_day) {
                $intervals[] = [$date->copy()->startOfDay(), $date->copy()->endOfDay()];

                continue;
            }

            if (! $block->start_time || ! $block->end_time) {
                continue;
            }

            $bs = Carbon::parse($date->format('Y-m-d').' '.$block->start_time);
            $be = Carbon::parse($date->format('Y-m-d').' '.$block->end_time);
            $intervals[] = [$bs, $be];
        }

        return $intervals;
    }

    protected static function blockAppliesToDate(FollowupCalendarBlockTiming $block, Carbon $date): bool
    {
        $anchor = Carbon::parse($block->block_date)->startOfDay();
        if ($date->lt($anchor)) {
            return false;
        }

        return match ($block->recurrence) {
            'none' => $date->isSameDay($anchor),
            'daily' => true,
            'weekly' => $date->dayOfWeekIso === $anchor->dayOfWeekIso,
            'monthly' => (int) $date->day === (int) $anchor->day,
            default => false,
        };
    }
}
