<?php

namespace App\Traits;

use App\Models\Admin;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

/**
 * ClientQueries Trait
 * 
 * Provides base query building and filtering logic for client operations.
 * Handles both admin and agent contexts automatically.
 */
trait ClientQueries
{
    /**
     * Get base client query with standard filters
     * 
     * @return Builder
     */
    protected function getBaseClientQuery(): Builder
    {
        $query = Admin::where('is_archived', '=', '0')
            ->where('role', '=', '7')
            ->whereNull('is_deleted');
            
        // Agent filtering - agents can only see their own clients
        if ($this->isAgentContext()) {
            $query->where('agent_id', Auth::user()->id);
        }
        
        return $query;
    }
    
    /**
     * Get archived client query
     * 
     * @return Builder
     */
    protected function getArchivedClientQuery(): Builder
    {
        $query = Admin::where('is_archived', '=', '1')
            ->where('role', '=', '7')
            ->whereNull('is_deleted');
            
        // Agent filtering
        if ($this->isAgentContext()) {
            $query->where('agent_id', Auth::user()->id);
        }
        
        return $query;
    }
    
    /**
     * Apply client filters from request
     * 
     * @param Builder $query
     * @param Request $request
     * @return Builder
     */
    protected function applyClientFilters(Builder $query, Request $request): Builder
    {
        // Client ID filter
        if ($request->has('client_id')) {
            $client_id = $request->input('client_id');
            if (trim($client_id) != '') {
                $query->where('client_id', '=', $client_id);
            }
        }
        
        // Type filter (admin only - agents don't have this)
        if (!$this->isAgentContext() && $request->has('type')) {
            $type = $request->input('type');
            if (trim($type) != '') {
                $query->where('type', 'LIKE', $type);
            }
        }
        
        // Name filter
        if ($request->has('name')) {
            $name = $request->input('name');
            if (trim($name) != '') {
                $query->where('first_name', 'LIKE', '%' . $name . '%');
            }
        }
        
        // Email filter
        if ($request->has('email')) {
            $email = $request->input('email');
            if (trim($email) != '') {
                // Admin can search in both email and att_email, agent only in email
                if ($this->isAgentContext()) {
                    $query->where('email', $email);
                } else {
                    $query->where(function($q) use ($email) {
                        $q->where('email', 'LIKE', '%' . $email . '%')
                          ->orWhere('att_email', 'LIKE', '%' . $email . '%');
                    });
                }
            }
        }
        
        // Phone filter
        if ($request->has('phone')) {
            $phone = $request->input('phone');
            if (trim($phone) != '') {
                // Admin can search in both phone and att_phone, agent only in phone
                if ($this->isAgentContext()) {
                    $query->where('phone', $phone);
                } else {
                    $query->where(function($q) use ($phone) {
                        $q->where('phone', 'LIKE', '%' . $phone . '%')
                          ->orWhere('att_phone', 'LIKE', '%' . $phone . '%');
                    });
                }
            }
        }
        
        return $query;
    }
    
    /**
     * Check if current context is agent
     * 
     * @return bool
     */
    protected function isAgentContext(): bool
    {
        // Check if authenticated as agent
        if (Auth::guard('agents')->check()) {
            return true;
        }
        
        // Check if admin user has agent role (for future unified auth)
        if (Auth::guard('admin')->check()) {
            // This can be extended if agents are migrated to admin table
            // For now, only check agent guard
            return false;
        }
        
        return false;
    }
    
    /**
     * Get empty client query (for users without access)
     * 
     * @return Builder
     */
    protected function getEmptyClientQuery(): Builder
    {
        return Admin::whereRaw('1 = 0');
    }
    
    /**
     * Get client by ID with context-aware filtering
     * 
     * @param int|string $id
     * @return Admin|null
     */
    protected function getClientById($id): ?Admin
    {
        $query = Admin::where('id', $id)
            ->where('role', '=', '7');
            
        // Agent filtering
        if ($this->isAgentContext()) {
            $query->where('agent_id', Auth::user()->id);
        }
        
        return $query->first();
    }
    
    /**
     * Get client by encoded ID with context-aware filtering
     * 
     * @param string $encodedId
     * @return Admin|null
     */
    protected function getClientByEncodedId(string $encodedId): ?Admin
    {
        $id = $this->decodeString($encodedId);
        if (!$id) {
            return null;
        }
        
        return $this->getClientById($id);
    }
}

