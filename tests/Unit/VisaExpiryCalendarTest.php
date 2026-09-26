<?php

namespace Tests\Unit;

use App\Models\Admin;
use App\Support\VisaExpiryCalendar;
use Tests\TestCase;

class VisaExpiryCalendarTest extends TestCase
{
    public function test_to_event_skips_missing_or_invalid_dates(): void
    {
        $missing = new Admin;
        $missing->id = 1;
        $missing->first_name = 'Jane';
        $missing->setRawAttributes(['id' => 1, 'first_name' => 'Jane', 'visaexpiry' => null], true);

        $invalid = new Admin;
        $invalid->id = 2;
        $invalid->first_name = 'Bob';
        $invalid->setRawAttributes(['id' => 2, 'first_name' => 'Bob', 'visaexpiry' => 'not-a-date'], true);

        $this->assertNull(VisaExpiryCalendar::toEvent($missing));
        $this->assertNull(VisaExpiryCalendar::toEvent($invalid));
    }

    public function test_to_event_keeps_existing_calendar_payload_shape(): void
    {
        $admin = new Admin;
        $admin->setRawAttributes([
            'id' => 42,
            'first_name' => 'Priya',
            'visaexpiry' => '2026-10-15',
        ], true);

        $event = VisaExpiryCalendar::toEvent($admin);

        $this->assertIsArray($event);
        $this->assertSame(42, $event['id']);
        $this->assertSame('Priya', $event['title']);
        $this->assertSame('2026-10-15', $event['start']);
        $this->assertSame('2026-10-15', $event['end']);
        $this->assertSame('October 15, 2026', $event['displayDate']);
        $this->assertSame('October 15, 2026', $event['extendedProps']['displayDate']);
        $this->assertSame(
            url('/clients/detail/'.base64_encode(convert_uuencode('42'))),
            $event['url']
        );
    }

    public function test_base_query_without_dates_does_not_restrict_the_range(): void
    {
        $sql = VisaExpiryCalendar::baseQuery()->toSql();

        $this->assertStringContainsString('visaexpiry', $sql);
        $this->assertStringContainsString('is not null', strtolower($sql));
        $this->assertStringNotContainsString('>=', $sql);
        $this->assertStringNotContainsString('<', $sql);
    }

    public function test_base_query_with_start_and_end_filters_the_visible_month(): void
    {
        $query = VisaExpiryCalendar::baseQuery('2026-09-01', '2026-10-01');
        $sql = strtolower($query->toSql());

        $this->assertStringContainsString('visaexpiry', $sql);
        $this->assertStringContainsString('>=', $sql);
        $this->assertStringContainsString('<', $sql);
        $this->assertSame(['2026-09-01', '2026-10-01'], $query->getBindings());
    }
}
