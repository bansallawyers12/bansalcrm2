<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

/**
 * Phase 5 partners performance regression checks.
 *
 * @extends TestCase
 */
#[Group('phase5')]
class Phase5PartnersTest extends TestCase
{
    public function test_partner_list_routes_are_registered(): void
    {
        $this->assertTrue(Route::has('partners.index'));
        $this->assertTrue(Route::has('partners.inactive'));
    }

    public function test_guests_cannot_open_phase5_partner_pages(): void
    {
        $this->get('/partners')->assertRedirect();
        $this->get('/partners-inactive')->assertRedirect();
    }

    public function test_partner_list_uses_paginated_query_with_counts_not_per_row_blade_queries(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/Admin/PartnersController.php'));
        $indexView = file_get_contents(resource_path('views/Admin/partners/index.blade.php'));
        $inactiveView = file_get_contents(resource_path('views/Admin/partners/inactive.blade.php'));

        $this->assertIsString($controller);
        $this->assertIsString($indexView);
        $this->assertIsString($inactiveView);

        $this->assertStringContainsString('paginatePartnerList', $controller);
        $this->assertStringContainsString('withCount', $controller);
        $this->assertStringContainsString('products as products_count', $controller);
        $this->assertStringContainsString('attachLatestListNotes', $controller);
        $this->assertStringContainsString('->select([', $controller);

        $this->assertStringNotContainsString('Product::where(', $indexView);
        $this->assertStringNotContainsString('Note::where(', $indexView);
        $this->assertStringNotContainsString('Product::where(', $inactiveView);
        $this->assertStringNotContainsString('Note::where(', $inactiveView);
        $this->assertStringContainsString('$list->products_count', $indexView);
        $this->assertStringContainsString('$list->latestListNote', $indexView);
    }

    public function test_partner_list_preserves_status_filters_and_default_sort(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/Admin/PartnersController.php'));

        $this->assertIsString($controller);
        $this->assertStringContainsString('paginatePartnerList($request, 0)', $controller);
        $this->assertStringContainsString('paginatePartnerList($request, 1)', $controller);
        $this->assertStringContainsString("->where('status', \$status)", $controller);
        $this->assertStringContainsString("->orderByDesc('student_count')", $controller);
        $this->assertStringContainsString("->where('partner_name', 'ilike'", $controller);
    }

    public function test_partner_products_relationship_casts_varchar_foreign_key_on_postgresql(): void
    {
        $partnerModel = file_get_contents(app_path('Models/Partner.php'));

        $this->assertIsString($partnerModel);
        $this->assertStringContainsString('CastingHasMany', $partnerModel);
        $this->assertStringContainsString("castingHasMany(Product::class, 'partner', 'id')", $partnerModel);
    }

    public function test_phase5_partners_indexes_migration_exists(): void
    {
        $files = glob(database_path('migrations/*_add_phase5_partners_performance_indexes.php'));

        $this->assertNotEmpty($files);
        $migration = file_get_contents($files[0]);

        $this->assertIsString($migration);
        $this->assertStringContainsString('partners_status_created_at_idx', $migration);
        $this->assertStringContainsString('notes_partner_list_note_idx', $migration);
    }
}
