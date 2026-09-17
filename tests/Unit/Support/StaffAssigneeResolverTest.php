<?php

namespace Tests\Unit\Support;

use App\Support\StaffAssigneeResolver;
use Tests\TestCase;

class StaffAssigneeResolverTest extends TestCase
{
    public function test_numeric_ids_skip_empty_and_null_assignee(): void
    {
        $this->assertSame([], StaffAssigneeResolver::numericIdsFromAssigneeValue(null));
        $this->assertSame([], StaffAssigneeResolver::numericIdsFromAssigneeValue(''));
        $this->assertSame([], StaffAssigneeResolver::numericIdsFromAssigneeValue('   '));
    }

    public function test_numeric_ids_parse_single_and_comma_separated_values(): void
    {
        $this->assertSame([1215], StaffAssigneeResolver::numericIdsFromAssigneeValue('1215'));
        $this->assertSame([1, 1215], StaffAssigneeResolver::numericIdsFromAssigneeValue('1,1215'));
        $this->assertSame([1, 1215], StaffAssigneeResolver::numericIdsFromAssigneeValue(' 1 , 1215 '));
    }

    public function test_numeric_ids_drop_empty_and_non_integer_segments(): void
    {
        $this->assertSame([1215], StaffAssigneeResolver::numericIdsFromAssigneeValue(',1215'));
        $this->assertSame([1, 1215], StaffAssigneeResolver::numericIdsFromAssigneeValue('1,,1215'));
        $this->assertSame([1], StaffAssigneeResolver::numericIdsFromAssigneeValue('1,abc'));
        $this->assertSame([], StaffAssigneeResolver::numericIdsFromAssigneeValue('abc'));
        $this->assertSame([], StaffAssigneeResolver::numericIdsFromAssigneeValue('0'));
    }

    public function test_empty_assignee_returns_empty_staff_collection_without_querying(): void
    {
        $empty = StaffAssigneeResolver::staffCollectionFromAssigneeValue('');
        $null = StaffAssigneeResolver::staffCollectionFromAssigneeValue(null);

        $this->assertCount(0, $empty);
        $this->assertCount(0, $null);
        $this->assertTrue($empty->isEmpty());
    }

    public function test_first_staff_still_returns_null_for_empty_assignee(): void
    {
        $this->assertNull(StaffAssigneeResolver::firstStaffFromAssigneeValue(null));
        $this->assertNull(StaffAssigneeResolver::firstStaffFromAssigneeValue(''));
        $this->assertNull(StaffAssigneeResolver::firstStaffFromAssigneeValue('   '));
    }
}
