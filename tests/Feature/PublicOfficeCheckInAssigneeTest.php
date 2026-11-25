<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class PublicOfficeCheckInAssigneeTest extends TestCase
{
    public function test_public_check_in_assignee_route_is_registered(): void
    {
        $this->assertTrue(Route::has('officevisits.public-checkin-assignee'));
        $this->assertSame(
            url('/office-visits/public-checkin-assignee'),
            route('officevisits.public-checkin-assignee')
        );
    }

    public function test_existing_change_assignee_route_still_exists(): void
    {
        $this->get('/office-visits/change_assignee')
            ->assertRedirect();
    }

    public function test_public_assignee_endpoint_requires_authentication(): void
    {
        $this->postJson(route('officevisits.public-checkin-assignee'), [
            'id' => 1,
            'assignee_id' => 1,
        ])->assertUnauthorized();
    }

    public function test_allowlist_config_contains_requested_staff_keys(): void
    {
        $keys = config('public_office_check_in.assignee_staff_keys');

        $this->assertIsArray($keys);
        foreach (['arun', 'ajay', 'pevy', 'ankit', 'sam', 'rakshita', 'sayed', 'ajneet', 'vijay', 'jsapreet'] as $key) {
            $this->assertContains($key, $keys);
        }
    }
}
