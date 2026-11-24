<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class PublicCheckInTest extends TestCase
{
    public function test_public_check_in_route_is_registered(): void
    {
        $this->assertTrue(Route::has('public.check-in.index'));
        $this->assertSame(url('/check-in'), route('public.check-in.index'));
    }

    public function test_a_visitor_can_open_check_in_without_logging_in(): void
    {
        $this->get(route('public.check-in.index'))
            ->assertOk()
            ->assertSee('Welcome to Bansal Immigration and Education')
            ->assertSee('Immigration')
            ->assertSee('Education')
            ->assertSee('href="'.e(config('services.immigration_check_in.url')).'"', false)
            ->assertSee('href="'.e(route('public.office-check-in.index')).'"', false);
    }

    public function test_admin_login_page_is_unchanged(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertDontSee('Welcome to Bansal Immigration and Education');
    }
}
