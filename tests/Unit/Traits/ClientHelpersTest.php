<?php

namespace Tests\Unit\Traits;

use App\Traits\ClientHelpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

/**
 * Test suite for ClientHelpers trait
 */
class ClientHelpersTest extends TestCase
{
    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test controller that uses the trait
        $this->controller = new class
        {
            use ClientHelpers;

            // Mock uploadFile and unlinkFile from base Controller
            public function uploadFile($file, $path)
            {
                return 'uploaded_file.jpg';
            }

            public function unlinkFile($file, $path)
            {
                return true;
            }
        };
    }

    /**
     * Test formatDateForDatabase converts DD/MM/YYYY to YYYY-MM-DD
     */
    public function test_format_date_for_database_converts_date_format()
    {
        $result = $this->controller->formatDateForDatabase('25/12/2023');

        $this->assertEquals('2023-12-25', $result);
    }

    /**
     * Test formatDateForDatabase returns null for empty input
     */
    public function test_format_date_for_database_returns_null_for_empty_input()
    {
        $result = $this->controller->formatDateForDatabase(null);

        $this->assertNull($result);
    }

    /**
     * Test formatDateForDatabase returns null for invalid format
     */
    public function test_format_date_for_database_returns_null_for_invalid_format()
    {
        $result = $this->controller->formatDateForDatabase('2023-12-25');

        $this->assertNull($result);
    }

    /**
     * Test formatDateForDisplay converts YYYY-MM-DD to DD/MM/YYYY
     */
    public function test_format_date_for_display_converts_date_format()
    {
        $result = $this->controller->formatDateForDisplay('2023-12-25');

        $this->assertEquals('25/12/2023', $result);
    }

    /**
     * Test formatDateForDisplay returns null for empty input
     */
    public function test_format_date_for_display_returns_null_for_empty_input()
    {
        $result = $this->controller->formatDateForDisplay(null);

        $this->assertNull($result);
    }

    /**
     * Test processRelatedFiles processes array correctly
     */
    public function test_process_related_files_processes_array_correctly()
    {
        $request = new Request([
            'related_files' => ['file1.pdf', 'file2.jpg', 'file3.docx'],
        ]);

        $result = $this->controller->processRelatedFiles($request);

        $this->assertEquals('file1.pdf,file2.jpg,file3.docx', $result);
    }

    /**
     * Test processRelatedFiles returns empty string for no files
     */
    public function test_process_related_files_returns_empty_string_for_no_files()
    {
        $request = new Request;

        $result = $this->controller->processRelatedFiles($request);

        $this->assertEquals('', $result);
    }

    public function test_normalize_related_client_ids_filters_empty_and_invalid_values(): void
    {
        $this->assertSame(
            ['123', '456', '789'],
            $this->controller->normalizeRelatedClientIds(['123', '', '456', '0', 'abc', ' 789 '])
        );
        $this->assertSame(['123', '456'], $this->controller->normalizeRelatedClientIds('123,,456,'));
        $this->assertSame([], $this->controller->normalizeRelatedClientIds(null));
    }

    /**
     * Test processFollowers processes array correctly
     */
    public function test_process_followers_processes_array_correctly()
    {
        $request = new Request([
            'followers' => [1, 2, 3],
        ]);

        $result = $this->controller->processFollowers($request);

        $this->assertEquals('1,2,3', $result);
    }

    /**
     * Test processTags processes array correctly
     */
    public function test_process_tags_processes_array_correctly()
    {
        $request = new Request([
            'tagname' => ['tag1', 'tag2', 'tag3'],
        ]);

        $result = $this->controller->processTags($request);

        $this->assertEquals('tag1,tag2,tag3', $result);
    }

    /**
     * Test generateClientId generates correct format
     */
    public function test_generate_client_id_generates_correct_format()
    {
        $result = $this->controller->generateClientId('John', 123);

        $this->assertStringStartsWith('JOHN', $result);
        $this->assertStringEndsWith('123', $result);
    }

    /**
     * Test getClientValidationRules returns correct rules for store
     */
    public function test_get_client_validation_rules_returns_correct_rules_for_store()
    {
        $request = new Request;

        $rules = $this->controller->getClientValidationRules($request);

        $this->assertArrayHasKey('first_name', $rules);
        $this->assertArrayHasKey('last_name', $rules);
        $this->assertArrayHasKey('email', $rules);
        $this->assertArrayHasKey('phone', $rules);
    }

    /**
     * Test getClientValidationRules includes client_id for update
     */
    public function test_get_client_validation_rules_includes_client_id_for_update()
    {
        $request = new Request;

        $rules = $this->controller->getClientValidationRules($request, 1);

        $this->assertArrayHasKey('client_id', $rules);
    }

    /**
     * Test getClientViewPath returns agent path for agents
     */
    public function test_get_client_view_path_returns_agent_path_for_agents()
    {
        Auth::shouldReceive('guard')
            ->with('agents')
            ->andReturnSelf();
        Auth::shouldReceive('check')
            ->andReturn(true);

        $result = $this->controller->getClientViewPath('clients.index');

        $this->assertEquals('Agent.clients.index', $result);
    }

    /**
     * Test getClientViewPath returns admin path for admins
     */
    public function test_get_client_view_path_returns_admin_path_for_admins()
    {
        Auth::shouldReceive('guard')
            ->with('agents')
            ->andReturnSelf();
        Auth::shouldReceive('check')
            ->andReturn(false);

        $result = $this->controller->getClientViewPath('clients.index');

        $this->assertEquals('Admin.clients.index', $result);
    }

    /**
     * Test encodeString encodes correctly
     */
    public function test_encode_string_encodes_correctly()
    {
        $result = $this->controller->encodeString('123');

        $this->assertNotEmpty($result);
        $this->assertIsString($result);
    }

    /**
     * Test decodeString decodes correctly
     */
    public function test_decode_string_decodes_correctly()
    {
        $encoded = $this->controller->encodeString('123');
        $decoded = $this->controller->decodeString($encoded);

        $this->assertEquals('123', $decoded);
    }

    /**
     * Test decodeString returns false for invalid input
     */
    public function test_decode_string_returns_false_for_invalid_input()
    {
        $result = $this->controller->decodeString('invalid_string');

        $this->assertFalse($result);
    }
}
