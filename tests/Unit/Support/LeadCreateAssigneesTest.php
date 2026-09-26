<?php

namespace Tests\Unit\Support;

use App\Support\LeadCreateAssignees;
use Tests\TestCase;

class LeadCreateAssigneesTest extends TestCase
{
    public function test_allowed_staff_ids_match_configured_lead_create_assignees(): void
    {
        $this->assertSame([
            1215,
            541,
            52594,
            1599,
            47134,
            51885,
        ], LeadCreateAssignees::allowedStaffIds());
    }

    public function test_lead_create_page_uses_assignable_staff_from_controller(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/Admin/LeadController.php'));
        $view = file_get_contents(resource_path('views/Admin/leads/create.blade.php'));

        $this->assertIsString($controller);
        $this->assertIsString($view);
        $this->assertStringContainsString('LeadCreateAssignees::assignableStaff()', $controller);
        $this->assertStringContainsString("compact('assignableStaff')", $controller);
        $this->assertStringContainsString('Rule::in(LeadCreateAssignees::allowedStaffIds())', $controller);
        $this->assertStringContainsString('$assignableStaff', $view);
        $this->assertStringContainsString('name="assign_to"', $view);
        $this->assertStringNotContainsString('name="assign_to[]"', $view);
        $this->assertStringNotContainsString('multiple="multiple"', $view);
        $this->assertStringNotContainsString('Staff::where(\'status\',1)', $view);
        $this->assertStringContainsString("'assign_to' => ['required', 'integer', Rule::in(LeadCreateAssignees::allowedStaffIds())]", $controller);
        $this->assertStringNotContainsString("'assign_to' => 'required|array|min:1'", $controller);
    }

    public function test_permitted_edit_ids_include_allowed_and_existing_assignees(): void
    {
        $this->assertSame(
            [1215, 541, 52594, 1599, 47134, 51885, 99999],
            LeadCreateAssignees::permittedStaffIdsForEdit([99999, 1215])
        );
    }

    public function test_client_edit_page_uses_assignable_staff_for_edit_from_controller(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/Admin/Client/ClientController.php'));
        $view = file_get_contents(resource_path('views/Admin/clients/edit.blade.php'));

        $this->assertIsString($controller);
        $this->assertIsString($view);
        $this->assertStringContainsString('LeadCreateAssignees::assignableStaffForEdit', $controller);
        $this->assertStringContainsString('LeadCreateAssignees::permittedStaffIdsForEdit', $controller);
        $this->assertStringContainsString('$assignableStaff', $view);
        $this->assertStringNotContainsString('Staff::where(\'status\', 1)', $view);
    }

    public function test_preferred_application_assignee_uses_first_client_assignee_when_allowed(): void
    {
        $this->assertSame(1215, LeadCreateAssignees::preferredApplicationAssigneeIdFromClientAssignee('1215,52594'));
        $this->assertNull(LeadCreateAssignees::preferredApplicationAssigneeIdFromClientAssignee('99999'));
        $this->assertNull(LeadCreateAssignees::preferredApplicationAssigneeIdFromClientAssignee(null));
    }

    public function test_add_application_modal_includes_restricted_assignee_field(): void
    {
        $partial = file_get_contents(resource_path('views/Admin/clients/partials/application-assignee-field.blade.php'));
        $controller = file_get_contents(app_path('Http/Controllers/Admin/Client/ClientApplicationController.php'));

        $this->assertIsString($partial);
        $this->assertIsString($controller);
        $this->assertStringContainsString('LeadCreateAssignees::assignableStaff()', $partial);
        $this->assertStringContainsString('name="assignee"', $partial);
        $this->assertStringContainsString('data-default-assignee', $partial);
        $this->assertStringContainsString('preferredApplicationAssigneeIdFromClientAssignee', $partial);
        $this->assertStringContainsString("['clientRecord' => \$fetchedData ?? null]", file_get_contents(resource_path('views/Admin/clients/addclientmodal.blade.php')));
        $this->assertStringContainsString('data-valid="required"', $partial);
        $this->assertStringContainsString('LeadCreateAssignees::allowedStaffIds()', $controller);
        $this->assertStringContainsString('$obj->user_id = $assigneeId;', $controller);
    }

    public function test_application_detail_grid_uses_inline_assignee_dropdown(): void
    {
        $view = file_get_contents(resource_path('views/Admin/clients/applicationdetail.blade.php'));
        $handlers = file_get_contents(public_path('js/pages/admin/client-detail/application-handlers.js'));

        $this->assertIsString($view);
        $this->assertIsString($handlers);
        $this->assertStringContainsString('application_grid_assignee_select', $view);
        $this->assertStringContainsString('assignableStaffForEdit', $view);
        $this->assertStringContainsString('saveApplicationAssignee', $handlers);
        $this->assertStringContainsString('#application_grid_assignee_select', $handlers);
    }

    public function test_client_edit_syncs_primary_assignee_to_all_client_applications_when_assign_to_changes(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/Admin/Client/ClientController.php'));

        $this->assertIsString($controller);
        $this->assertStringContainsString('syncApplicationsToPrimaryClientAssignee', $controller);
        $this->assertStringContainsString("->where('client_id', \$clientId)", $controller);
        $this->assertStringContainsString('$assigneeChanged', $controller);
        $this->assertStringContainsString("strcasecmp((string) (\$obj->type ?? ''), 'client') === 0", $controller);
    }
}
