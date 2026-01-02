<?php

namespace Tests\Unit\Traits;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Mockery;

/**
 * Test suite for ClientAuthorization trait
 */
class ClientAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test controller that uses the trait
        $this->controller = new class {
            use \App\Traits\ClientAuthorization;
        };
    }

    /**
     * Test hasModuleAccess returns true for admin with access
     */
    public function test_hasModuleAccess_returns_true_for_admin_with_access()
    {
        $userRole = Mockery::mock(UserRole::class);
        $userRole->module_access = json_encode(['20' => true]);
        
        $user = Mockery::mock('user');
        $user->role = 1;
        
        Auth::shouldReceive('guard')
            ->with('agents')
            ->andReturnSelf();
        Auth::shouldReceive('check')
            ->andReturn(false);
        Auth::shouldReceive('guard')
            ->with('admin')
            ->andReturnSelf();
        Auth::shouldReceive('check')
            ->andReturn(true);
        Auth::shouldReceive('user')
            ->andReturn($user);
        
        UserRole::shouldReceive('find')
            ->with(1)
            ->andReturn($userRole);
        
        $result = $this->controller->hasModuleAccess('20');
        
        $this->assertTrue($result);
    }

    /**
     * Test hasModuleAccess returns false for admin without access
     */
    public function test_hasModuleAccess_returns_false_for_admin_without_access()
    {
        $userRole = Mockery::mock(UserRole::class);
        $userRole->module_access = json_encode(['21' => true]); // Different module
        
        $user = Mockery::mock('user');
        $user->role = 1;
        
        Auth::shouldReceive('guard')
            ->with('agents')
            ->andReturnSelf();
        Auth::shouldReceive('check')
            ->andReturn(false);
        Auth::shouldReceive('guard')
            ->with('admin')
            ->andReturnSelf();
        Auth::shouldReceive('check')
            ->andReturn(true);
        Auth::shouldReceive('user')
            ->andReturn($user);
        
        UserRole::shouldReceive('find')
            ->with(1)
            ->andReturn($userRole);
        
        $result = $this->controller->hasModuleAccess('20');
        
        $this->assertFalse($result);
    }

    /**
     * Test hasModuleAccess returns true for agents
     */
    public function test_hasModuleAccess_returns_true_for_agents()
    {
        Auth::shouldReceive('guard')
            ->with('agents')
            ->andReturnSelf();
        Auth::shouldReceive('check')
            ->andReturn(true);
        
        $result = $this->controller->hasModuleAccess('20');
        
        $this->assertTrue($result);
    }

    /**
     * Test isAgentUser returns true for agents
     */
    public function test_isAgentUser_returns_true_for_agents()
    {
        Auth::shouldReceive('guard')
            ->with('agents')
            ->andReturnSelf();
        Auth::shouldReceive('check')
            ->andReturn(true);
        
        $result = $this->controller->isAgentUser();
        
        $this->assertTrue($result);
    }

    /**
     * Test isAgentUser returns false for admins
     */
    public function test_isAgentUser_returns_false_for_admins()
    {
        Auth::shouldReceive('guard')
            ->with('agents')
            ->andReturnSelf();
        Auth::shouldReceive('check')
            ->andReturn(false);
        
        $result = $this->controller->isAgentUser();
        
        $this->assertFalse($result);
    }

    /**
     * Test isAdminUser returns true for admins
     */
    public function test_isAdminUser_returns_true_for_admins()
    {
        Auth::shouldReceive('guard')
            ->with('admin')
            ->andReturnSelf();
        Auth::shouldReceive('check')
            ->andReturn(true);
        
        $result = $this->controller->isAdminUser();
        
        $this->assertTrue($result);
    }

    /**
     * Test canViewClient returns true for admin with access
     */
    public function test_canViewClient_returns_true_for_admin_with_access()
    {
        $client = Mockery::mock(Admin::class);
        
        $userRole = Mockery::mock(UserRole::class);
        $userRole->module_access = json_encode(['20' => true]);
        
        $user = Mockery::mock('user');
        $user->role = 1;
        
        Auth::shouldReceive('guard')
            ->with('admin')
            ->andReturnSelf();
        Auth::shouldReceive('check')
            ->andReturn(true);
        Auth::shouldReceive('user')
            ->andReturn($user);
        
        UserRole::shouldReceive('find')
            ->with(1)
            ->andReturn($userRole);
        
        $result = $this->controller->canViewClient($client);
        
        $this->assertTrue($result);
    }

    /**
     * Test canViewClient returns true for agent with own client
     */
    public function test_canViewClient_returns_true_for_agent_with_own_client()
    {
        $client = Mockery::mock(Admin::class);
        $client->agent_id = 1;
        
        $agent = Mockery::mock('agent');
        $agent->id = 1;
        
        Auth::shouldReceive('guard')
            ->with('admin')
            ->andReturnSelf();
        Auth::shouldReceive('check')
            ->andReturn(false);
        Auth::shouldReceive('guard')
            ->with('agents')
            ->andReturnSelf();
        Auth::shouldReceive('check')
            ->andReturn(true);
        Auth::shouldReceive('user')
            ->andReturn($agent);
        
        $result = $this->controller->canViewClient($client);
        
        $this->assertTrue($result);
    }

    /**
     * Test canViewClient returns false for agent with other's client
     */
    public function test_canViewClient_returns_false_for_agent_with_others_client()
    {
        $client = Mockery::mock(Admin::class);
        $client->agent_id = 2;
        
        $agent = Mockery::mock('agent');
        $agent->id = 1;
        
        Auth::shouldReceive('guard')
            ->with('admin')
            ->andReturnSelf();
        Auth::shouldReceive('check')
            ->andReturn(false);
        Auth::shouldReceive('guard')
            ->with('agents')
            ->andReturnSelf();
        Auth::shouldReceive('check')
            ->andReturn(true);
        Auth::shouldReceive('user')
            ->andReturn($agent);
        
        $result = $this->controller->canViewClient($client);
        
        $this->assertFalse($result);
    }

    /**
     * Test canEditClient returns true for admin with access
     */
    public function test_canEditClient_returns_true_for_admin_with_access()
    {
        $client = Mockery::mock(Admin::class);
        
        $userRole = Mockery::mock(UserRole::class);
        $userRole->module_access = json_encode(['20' => true]);
        
        $user = Mockery::mock('user');
        $user->role = 1;
        
        Auth::shouldReceive('guard')
            ->with('admin')
            ->andReturnSelf();
        Auth::shouldReceive('check')
            ->andReturn(true);
        Auth::shouldReceive('user')
            ->andReturn($user);
        
        UserRole::shouldReceive('find')
            ->with(1)
            ->andReturn($userRole);
        
        $result = $this->controller->canEditClient($client);
        
        $this->assertTrue($result);
    }

    /**
     * Test canDeleteClient returns true only for admins
     */
    public function test_canDeleteClient_returns_true_only_for_admins()
    {
        $client = Mockery::mock(Admin::class);
        
        $userRole = Mockery::mock(UserRole::class);
        $userRole->module_access = json_encode(['20' => true]);
        
        $user = Mockery::mock('user');
        $user->role = 1;
        
        Auth::shouldReceive('guard')
            ->with('admin')
            ->andReturnSelf();
        Auth::shouldReceive('check')
            ->andReturn(true);
        Auth::shouldReceive('user')
            ->andReturn($user);
        
        UserRole::shouldReceive('find')
            ->with(1)
            ->andReturn($userRole);
        
        $result = $this->controller->canDeleteClient($client);
        
        $this->assertTrue($result);
    }

    /**
     * Test canDeleteClient returns false for agents
     */
    public function test_canDeleteClient_returns_false_for_agents()
    {
        $client = Mockery::mock(Admin::class);
        
        Auth::shouldReceive('guard')
            ->with('admin')
            ->andReturnSelf();
        Auth::shouldReceive('check')
            ->andReturn(false);
        
        $result = $this->controller->canDeleteClient($client);
        
        $this->assertFalse($result);
    }

    /**
     * Test getCurrentUserRole returns correct role
     */
    public function test_getCurrentUserRole_returns_correct_role()
    {
        Auth::shouldReceive('guard')
            ->with('agents')
            ->andReturnSelf();
        Auth::shouldReceive('check')
            ->andReturn(true);
        
        $result = $this->controller->getCurrentUserRole();
        
        $this->assertEquals('agent', $result);
    }
}

