<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class PublicOfficeCheckInTest extends TestCase
{
    public function test_public_office_check_in_route_is_registered(): void
    {
        $this->assertTrue(Route::has('public.office-check-in.index'));
        $this->assertTrue(Route::has('public.office-check-in.lookup'));
        $this->assertTrue(Route::has('public.office-check-in.submit'));
        $this->assertTrue(Route::has('public.office-check-in.create-lead'));
        $this->assertSame(url('/office-check-in'), route('public.office-check-in.index'));
        $this->assertNotSame(route('public.check-in.index'), route('public.office-check-in.index'));
    }

    public function test_a_visitor_can_open_office_check_in_without_logging_in(): void
    {
        $this->get(route('public.office-check-in.index'))
            ->assertOk()
            ->assertSee('Office Check-In')
            ->assertSee('Tell us you have arrived')
            ->assertSee('Qualification')
            ->assertSee('Work Experience')
            ->assertDontSee('Welcome to Bansal Immigration and Education');
    }

    public function test_lookup_requires_phone(): void
    {
        $this->postJson(route('public.office-check-in.lookup'), [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['phone']);
    }

    public function test_lookup_rejects_invalid_australian_phone_values(): void
    {
        $this->postJson(route('public.office-check-in.lookup'), [
            'phone' => '12345',
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['phone']);
    }

    public function test_existing_office_visits_waiting_route_still_exists(): void
    {
        $this->assertTrue(Route::has('officevisits.waiting'));
        $this->assertSame(url('/office-visits/waiting'), route('officevisits.waiting'));
    }
}
