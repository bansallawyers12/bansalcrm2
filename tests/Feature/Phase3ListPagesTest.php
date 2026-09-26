<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class Phase3ListPagesTest extends TestCase
{
    public function test_list_and_visa_report_routes_are_registered(): void
    {
        $this->assertTrue(Route::has('clients.index'));
        $this->assertTrue(Route::has('leads.index'));
        $this->assertTrue(Route::has('clients.archived'));
        $this->assertTrue(Route::has('reports.visaexpires'));
        $this->assertTrue(Route::has('reports.visaexpires.events'));
    }

    public function test_guests_cannot_open_phase3_pages(): void
    {
        $this->get('/clients')->assertRedirect();
        $this->get('/leads')->assertRedirect();
        $this->get('/archived')->assertRedirect();
        $this->get('/reports/visaexpires')->assertRedirect();
        $this->get('/reports/visaexpires/events')->assertRedirect();
    }

    public function test_visa_report_view_loads_events_by_month_instead_of_dumping_all_rows(): void
    {
        $view = file_get_contents(resource_path('views/Admin/reports/visaexpires.blade.php'));

        $this->assertIsString($view);
        $this->assertStringContainsString('visaExpiresEventsUrl', $view);
        $this->assertStringContainsString('window.open(info.event.url', $view);
        $this->assertStringNotContainsString('\\App\\Models\\Admin::select', $view);
        $this->assertStringNotContainsString('CAST(visaexpiry AS TEXT)', $view);
    }

    public function test_client_list_still_loads_application_counts_and_paginator_totals(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/Admin/Client/ClientController.php'));

        $this->assertIsString($controller);
        $this->assertStringContainsString('in_progress_applications_count', $controller);
        $this->assertStringContainsString('loadCount', $controller);
        $this->assertStringContainsString('$totalData = $lists->total();', $controller);
        $this->assertStringContainsString('paginate(20)', $controller);
    }

    public function test_client_type_filter_still_uses_case_insensitive_match(): void
    {
        $queries = file_get_contents(app_path('Traits/ClientQueries.php'));

        $this->assertIsString($queries);
        $this->assertStringContainsString("where('type', 'ilike', 'client')", $queries);
    }

    public function test_lead_and_archived_lists_use_paginator_total_not_a_second_count(): void
    {
        $leads = file_get_contents(app_path('Http/Controllers/Admin/LeadController.php'));
        $clients = file_get_contents(app_path('Http/Controllers/Admin/Client/ClientController.php'));

        $this->assertIsString($leads);
        $this->assertIsString($clients);
        $this->assertStringContainsString('$totalData = $lists->total();', $leads);
        $this->assertStringNotContainsString('(clone $baseQuery)->count()', $leads);
        $this->assertStringContainsString('$totalData = $lists->total();', $clients);
    }
}
