<?php

namespace Tests\Feature\Elite;

use App\Models\Staff;
use Tests\TestCase;

class EliteInboxDateRangeTest extends TestCase
{
    private function actingAsStaff(): Staff
    {
        $staff = new Staff([
            'first_name' => 'Test',
            'last_name' => 'Staff',
            'email' => 'staff@example.com',
            'password' => 'secret',
        ]);
        $staff->id = 1;

        $this->actingAs($staff, 'admin');

        return $staff;
    }

    public function test_elite_inbox_page_defaults_to_last_thirty_days(): void
    {
        $this->actingAsStaff();

        $response = $this->get(route('elite.emails.index'));

        $response->assertOk();
        $response->assertSee('value="30"', false);
        $response->assertSee('selected', false);
    }

    public function test_elite_inbox_json_accepts_all_time_range_flag(): void
    {
        $this->actingAsStaff();

        $response = $this->getJson(route('elite.emails.inbox', ['date_range' => 'all']));

        $response->assertOk();
        $response->assertJsonStructure([
            'emails',
            'folder',
            'account',
            'accounts',
            'message',
        ]);
    }

    public function test_elite_inbox_json_defaults_without_explicit_all_time_flag(): void
    {
        $this->actingAsStaff();

        $response = $this->getJson(route('elite.emails.inbox'));

        $response->assertOk();
        $response->assertJsonPath('folder', 'inbox');
    }
}
