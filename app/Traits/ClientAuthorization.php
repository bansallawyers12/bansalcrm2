<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use App\Models\Admin;

/**
 * ClientAuthorization Trait
 * 
 * Provides authorization and permission checking logic for client operations.
 * Handles both admin and agent contexts.
 */
trait ClientAuthorization
{
    /**
     * Check if user has module access
     * 
     * @param string $moduleId Module ID to check (e.g., '20' for clients)
     * @return bool
     */
    protected function hasModuleAccess(string $moduleId): bool
    {
        // Agents don't have module access checks
        if ($this->isAgentUser()) {
            return true; // Agents have access to their own clients
        }
        
        // Admin users need module access check
        if (Auth::guard('admin')->check()) {
            $roles = \App\Models\UserRole::find(Auth::user()->role);
            if (!$roles) {
                return false;
            }
            
            $module_access = json_decode($roles->module_access, true);
            return array_key_exists($moduleId, $module_access);
        }
        
        return false;
    }
    
    /**
     * Check if current user is an agent (deprecated - agents don't have login access)
     * 
     * @return bool Always returns false since agents don't log in
     */
    protected function isAgentUser(): bool
    {
        // Agents don't have login access - they exist only as records/accounting
        return false;
    }
    
    /**
     * Check if current user is an admin
     * 
     * @return bool
     */
    protected function isAdminUser(): bool
    {
        return Auth::guard('admin')->check();
    }
    
    /**
     * Check if user can view a specific client
     * 
     * @param Admin $client
     * @return bool
     */
    protected function canViewClient(Admin $client): bool
    {
        // Admins can view all clients (if they have module access)
        if ($this->isAdminUser()) {
            return $this->hasModuleAccess('20');
        }
        
        // Agents can only view their own clients
        if ($this->isAgentUser()) {
            return $client->agent_id == Auth::user()->id;
        }
        
        return false;
    }
    
    /**
     * Check if user can edit a specific client
     * 
     * @param Admin $client
     * @return bool
     */
    protected function canEditClient(Admin $client): bool
    {
        // Admins can edit all clients (if they have module access)
        if ($this->isAdminUser()) {
            return $this->hasModuleAccess('20');
        }
        
        // Agents can only edit their own clients
        if ($this->isAgentUser()) {
            return $client->agent_id == Auth::user()->id;
        }
        
        return false;
    }
    
    /**
     * Check if user can delete a specific client
     * 
     * @param Admin $client
     * @return bool
     */
    protected function canDeleteClient(Admin $client): bool
    {
        // Only admins can delete clients
        if ($this->isAdminUser()) {
            return $this->hasModuleAccess('20');
        }
        
        // Agents cannot delete clients
        return false;
    }
    
    /**
     * Check if user can view all clients (not just their own)
     * 
     * @return bool
     */
    protected function canViewAllClients(): bool
    {
        // Only admins with module access can view all clients
        return $this->isAdminUser() && $this->hasModuleAccess('20');
    }
    
    /**
     * Get current user's role identifier
     * 
     * @return string
     */
    protected function getCurrentUserRole(): string
    {
        if ($this->isAgentUser()) {
            return 'agent';
        }
        
        if ($this->isAdminUser()) {
            return 'admin';
        }
        
        return 'guest';
    }
    
    /**
     * Check if user can assign clients to other users
     * 
     * @return bool
     */
    protected function canAssignClients(): bool
    {
        // Only admins can assign clients
        return $this->isAdminUser() && $this->hasModuleAccess('20');
    }
}

