<?php

namespace App\Support;

use App\Models\Admin;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class VisaExpiryCalendar
{
    /**
     * FullCalendar event payloads for visa expiry dates.
     *
     * When FullCalendar sends start/end, only that visible range is loaded.
     * Without dates, all expiry rows are returned (same completeness as before).
     *
     * @return list<array{id: int, title: string, start: string, end: string, url: string, displayDate: string}>
     */
    public static function events(?string $start = null, ?string $end = null): array
    {
        $events = [];
        foreach (self::baseQuery($start, $end)->get() as $row) {
            $event = self::toEvent($row);
            if ($event !== null) {
                $events[] = $event;
            }
        }

        return $events;
    }

    /**
     * Same filters as the previous Blade query, plus an optional visible-range window.
     */
    public static function baseQuery(?string $start = null, ?string $end = null): Builder
    {
        $query = Admin::query()
            ->select('id', 'visaexpiry', 'first_name', 'last_name')
            ->whereNotNull('visaexpiry');

        self::constrainToVisibleRange($query, $start, $end);

        return $query;
    }

    /**
     * @return array{id: int, title: string, start: string, end: string, url: string, displayDate: string}|null
     */
    public static function toEvent(Admin $row): ?array
    {
        $raw = $row->visaExpiry;
        if ($raw === null || $raw === '' || ! strtotime((string) $raw)) {
            return null;
        }

        $timestamp = strtotime((string) $raw);

        return [
            'id' => (int) $row->id,
            'title' => htmlspecialchars((string) $row->first_name, ENT_QUOTES, 'UTF-8'),
            'start' => date('Y-m-d', $timestamp),
            'end' => date('Y-m-d', $timestamp),
            'url' => url('/clients/detail/'.base64_encode(convert_uuencode((string) $row->id))),
            'displayDate' => date('F d, Y', $timestamp),
            'extendedProps' => [
                'displayDate' => date('F d, Y', $timestamp),
            ],
        ];
    }

    private static function constrainToVisibleRange(Builder $query, ?string $start, ?string $end): void
    {
        $from = self::parseDate($start);
        $to = self::parseDate($end);

        if ($from !== null) {
            $query->whereDate('visaexpiry', '>=', $from);
        }

        if ($to !== null) {
            $query->whereDate('visaexpiry', '<', $to);
        }
    }

    private static function parseDate(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        try {
            return Carbon::parse($value)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }
}
