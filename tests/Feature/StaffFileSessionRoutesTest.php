<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class StaffFileSessionRoutesTest extends TestCase
{
    public function test_guest_cannot_post_session_heartbeat(): void
    {
        $this->postJson(route('dashboard.my-day.sessions.heartbeat'), [
            'record_type' => 'student',
            'record_id' => 1,
            'focused_seconds' => 10,
        ])->assertUnauthorized();
    }

    public function test_session_routes_are_registered(): void
    {
        $this->assertTrue(Route::has('dashboard.my-day.sessions.heartbeat'));
        $this->assertTrue(Route::has('dashboard.my-day.sessions.blur'));
        $this->assertTrue(Route::has('dashboard.my-day.sessions.idle-cut'));
        $this->assertTrue(Route::has('dashboard.my-day.sessions.update'));
        $this->assertTrue(Route::has('dashboard.my-day.sessions.destroy'));
        $this->assertTrue(Route::has('dashboard.my-day.diary'));
        $this->assertTrue(Route::has('dashboard.my-day.file-time.log'));
        $this->assertTrue(Route::has('dashboard.my-day.copy-summary'));
    }
}
