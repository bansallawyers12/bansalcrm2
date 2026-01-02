<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 * ClientHelpers Trait
 * 
 * Provides helper methods for client operations including:
 * - File uploads and management
 * - Data validation and formatting
 * - Date handling
 * - String encoding/decoding
 */
trait ClientHelpers
{
    /**
     * Upload a file
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $filePath Config path (e.g., 'constants.profile_imgs')
     * @return string|null Uploaded filename or null on failure
     */
    protected function uploadClientFile($file, string $filePath): ?string
    {
        if (!$file || !$file->isValid()) {
            return null;
        }
        
        $configPath = Config::get($filePath);
        if (!$configPath) {
            return null;
        }
        
        return $this->uploadFile($file, $configPath);
    }
    
    /**
     * Delete/unlink a file
     * 
     * @param string $fileName
     * @param string $filePath Config path
     * @return bool
     */
    protected function deleteClientFile(string $fileName, string $filePath): bool
    {
        if (empty($fileName)) {
            return false;
        }
        
        $configPath = Config::get($filePath);
        if (!$configPath) {
            return false;
        }
        
        $this->unlinkFile($fileName, $configPath);
        return true;
    }
    
    /**
     * Format date from DD/MM/YYYY to YYYY-MM-DD
     * 
     * @param string|null $date Date in DD/MM/YYYY format
     * @return string|null Date in YYYY-MM-DD format or null
     */
    protected function formatDateForDatabase(?string $date): ?string
    {
        if (empty($date)) {
            return null;
        }
        
        $parts = explode('/', $date);
        if (count($parts) !== 3) {
            return null;
        }
        
        return $parts[2] . '-' . $parts[1] . '-' . $parts[0];
    }
    
    /**
     * Format date from YYYY-MM-DD to DD/MM/YYYY
     * 
     * @param string|null $date Date in YYYY-MM-DD format
     * @return string|null Date in DD/MM/YYYY format or null
     */
    protected function formatDateForDisplay(?string $date): ?string
    {
        if (empty($date)) {
            return null;
        }
        
        try {
            $carbon = Carbon::parse($date);
            return $carbon->format('d/m/Y');
        } catch (\Exception $e) {
            return null;
        }
    }
    
    /**
     * Process related files array from request
     * 
     * @param Request $request
     * @param string $fieldName Field name in request (default: 'related_files')
     * @return string Comma-separated string of file names
     */
    protected function processRelatedFiles(Request $request, string $fieldName = 'related_files'): string
    {
        $relatedFiles = '';
        
        if ($request->has($fieldName) && is_array($request->input($fieldName))) {
            $files = $request->input($fieldName);
            foreach ($files as $file) {
                if (!empty($file)) {
                    $relatedFiles .= $file . ',';
                }
            }
        }
        
        return rtrim($relatedFiles, ',');
    }
    
    /**
     * Process followers array from request
     * 
     * @param Request $request
     * @return string Comma-separated string of follower IDs
     */
    protected function processFollowers(Request $request): string
    {
        $followers = '';
        
        if ($request->has('followers') && is_array($request->input('followers'))) {
            foreach ($request->input('followers') as $follower) {
                if (!empty($follower)) {
                    $followers .= $follower . ',';
                }
            }
        }
        
        return rtrim($followers, ',');
    }
    
    /**
     * Process tags array from request
     * 
     * @param Request $request
     * @return string Comma-separated string of tag names
     */
    protected function processTags(Request $request): string
    {
        if ($request->has('tagname') && is_array($request->input('tagname'))) {
            return implode(',', $request->input('tagname'));
        }
        
        return '';
    }
    
    /**
     * Generate client ID from first name and ID
     * 
     * @param string $firstName
     * @param int $clientId
     * @return string
     */
    protected function generateClientId(string $firstName, int $clientId): string
    {
        $firstFour = strtoupper(substr($firstName, 0, 4));
        $yearMonth = date('ym');
        return $firstFour . $yearMonth . $clientId;
    }
    
    /**
     * Validate client data for store operation
     * 
     * @param Request $request
     * @param int|null $clientId For update operations
     * @return array Validation rules
     */
    protected function getClientValidationRules(Request $request, ?int $clientId = null): array
    {
        $rules = [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|max:255|unique:admins,email',
            'phone' => 'required|max:255|unique:admins,phone',
        ];
        
        // For updates, exclude current client from unique checks
        if ($clientId) {
            $rules['email'] = 'required|max:255|unique:admins,email,' . $clientId;
            $rules['phone'] = 'required|max:255|unique:admins,phone,' . $clientId;
            
            // Client ID is required for updates
            $rules['client_id'] = 'required|max:255|unique:admins,client_id,' . $clientId;
        }
        
        return $rules;
    }
    
    /**
     * Get view path based on user context
     * 
     * @param string $viewName View name without prefix (e.g., 'clients.index')
     * @return string Full view path
     */
    protected function getClientViewPath(string $viewName): string
    {
        if ($this->isAgentContext()) {
            return 'Agent.' . $viewName;
        }
        
        return 'Admin.' . $viewName;
    }
    
    /**
     * Get redirect URL for client operations
     * 
     * @param string $action Action name (e.g., 'index', 'detail')
     * @param mixed $id Optional ID for detail/edit operations
     * @return string Redirect URL
     */
    protected function getClientRedirectUrl(string $action, $id = null): string
    {
        $basePath = '/clients'; // Unified route (will be implemented in Phase 4)
        
        // For now, use context-aware paths
        if ($this->isAgentContext()) {
            $basePath = '/agent/clients';
        } else {
            $basePath = '/admin/clients';
        }
        
        switch ($action) {
            case 'index':
                return $basePath;
            case 'detail':
            case 'edit':
                return $basePath . '/' . $action . '/' . $this->encodeString($id);
            default:
                return $basePath;
        }
    }
    
    // Note: isAgentContext() is defined in ClientQueries trait
    // When both traits are used, ClientQueries::isAgentContext() will be used
    
    // Note: encodeString() and decodeString() are already defined in the base Controller class
    // They are public methods, so they're available to all controllers
    // No need to redefine them here
}

