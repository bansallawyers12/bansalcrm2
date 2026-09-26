<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

/**
 * Phase 4 actions performance regression checks.
 *
 * @extends TestCase
 */
#[Group('phase4')]
class Phase4ActionsTest extends TestCase
{
    public function test_action_routes_are_registered(): void
    {
        $this->assertTrue(Route::has('action.index'));
        $this->assertTrue(Route::has('action.list'));
        $this->assertTrue(Route::has('action.assigned_by_me'));
    }

    public function test_guests_cannot_open_phase4_action_pages(): void
    {
        $this->get('/action')->assertRedirect();
        $this->get('/action/list')->assertRedirect();
        $this->get('/action/assigned-by-me')->assertRedirect();
    }

    public function test_action_list_uses_eloquent_datatables_not_full_get(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/Admin/ActionController.php'));

        $this->assertIsString($controller);
        $this->assertStringContainsString('DataTables::eloquent($query)', $controller);
        $this->assertStringNotContainsString(")->orderByRaw('created_at DESC NULLS LAST')->get();", $controller);
    }

    public function test_action_list_preserves_all_task_group_filter_set(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/Admin/ActionController.php'));

        $this->assertIsString($controller);
        $this->assertStringContainsString('task_group_filter', $controller);
        $this->assertStringContainsString("'Personal Task'", $controller);
        $this->assertStringContainsString("orWhere('task_group', 'ilike', 'stage')", $controller);
    }

    public function test_assigned_by_me_loads_staff_once_and_uses_assigned_user_relation(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/Admin/ActionController.php'));
        $view = file_get_contents(resource_path('views/Admin/action/assigned_by_me.blade.php'));

        $this->assertIsString($controller);
        $this->assertIsString($view);
        $this->assertStringContainsString('$assignableStaff', $controller);
        $this->assertStringContainsString('assignedByMeBaseQuery', $controller);
        $this->assertStringNotContainsString('Staff::find($list->assigned_to)', $view);
        $this->assertStringNotContainsString("Staff::where('status',1)", $view);
        $this->assertStringContainsString('$list->assigned_user', $view);
        $this->assertStringContainsString('actionPopoverModal', $view);
    }

    public function test_phase4_actions_indexes_migration_exists(): void
    {
        $files = glob(database_path('migrations/*_add_phase4_actions_performance_indexes.php'));

        $this->assertNotEmpty($files);
        $migration = file_get_contents($files[0]);

        $this->assertIsString($migration);
        $this->assertStringContainsString('notes_assigned_to_status_assign_date_idx', $migration);
        $this->assertStringContainsString('notes_user_open_actions_idx', $migration);
    }
}
