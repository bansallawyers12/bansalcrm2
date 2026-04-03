<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\Staff;
use App\Services\CrmAccess\CrmAccessService;
use App\Support\StaffClientVisibility;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class SearchService
{
    protected $query;
    protected $limit;
    protected $cacheEnabled;

    public function __construct($query, $limit = 50, $cacheEnabled = true)
    {
        $this->query = $this->sanitizeQuery($query);
        $this->limit = $limit;
        $this->cacheEnabled = $cacheEnabled;
    }

    /**
     * Sanitize and validate search query
     */
    protected function sanitizeQuery($query)
    {
        // Remove dangerous characters but keep useful ones
        $query = strip_tags($query);
        $query = trim($query);
        
        return $query;
    }

    /**
     * Safely encode client/lead ID for URL
     */
    protected function encodeId($id)
    {
        if (empty($id) || !is_numeric($id)) {
            return '';
        }
        
        try {
            $encoded = convert_uuencode((string)$id);
            return base64_encode($encoded);
        } catch (\Exception $e) {
            // Fallback to simple base64 encoding if convert_uuencode fails
            return base64_encode((string)$id);
        }
    }

    /**
     * Main search method
     */
    public function search()
    {
        if (strlen($this->query) < 2) {
            return $this->emptyResponse();
        }

        $cacheKey = 'search:' . md5($this->query . ':' . $this->limit . ':' . $this->visibilityCacheSuffix());

        if ($this->cacheEnabled) {
            $user = Auth::guard('admin')->user();
            $ttl = 300;
            if ($user instanceof Staff
                && StaffClientVisibility::strictAllocationEnabled()
                && ! StaffClientVisibility::isExemptFromAllocation($user)) {
                // Short TTL so Quick access / grants refresh locked flags in the dropdown quickly
                $ttl = 15;
            }

            return Cache::remember($cacheKey, $ttl, function () {
                return $this->performSearch();
            });
        }

        return $this->performSearch();
    }

    /**
     * Per-staff cache segment: every logged-in staff user gets their own search cache namespace so
     * locked / grant flags cannot leak across users. {@see bumpGlobalSearchCacheForStaff()} increments
     * the revision when cross-access grants change so Quick access is reflected immediately.
     */
    protected function visibilityCacheSuffix(): string
    {
        $user = Auth::guard('admin')->user();
        if ($user instanceof Staff) {
            $staffId = (int) $user->id;
            $rev = (int) Cache::get('search:staff_rev:' . $staffId, 0);

            return 'u' . $staffId . ':' . $rev;
        }

        return 'all';
    }

    /**
     * Invalidate cached header/global search for this staff member (grant created, approved, revoked, expired).
     */
    public static function bumpGlobalSearchCacheForStaff(int $staffId): void
    {
        if ($staffId <= 0) {
            return;
        }
        $key = 'search:staff_rev:' . $staffId;
        Cache::put($key, (int) Cache::get($key, 0) + 1);
    }

    /**
     * Same rules as {@see searchResultNeedsAccessModal} in modern-search.js: open Quick access first when
     * the staff member has no active temp grant and (record is locked OR BI-style search gating applies).
     */
    public static function staffShouldGateClientNavigation(int $adminId, Staff $user): bool
    {
        if ($adminId <= 0) {
            return false;
        }

        if (app(CrmAccessService::class)->hasActiveGrant($user, $adminId)) {
            return false;
        }

        if (! StaffClientVisibility::canAccessAdminRecord($adminId, $user)) {
            return true;
        }

        $noClientsModule = ! StaffClientVisibility::staffHasClientsModule($user);

        return $noClientsModule && (bool) ($user->quick_access_enabled ?? false);
    }

    protected function applyStaffVisibilityToAdminQuery(Builder $query): Builder
    {
        $user = Auth::guard('admin')->user();
        if ($user instanceof Staff) {
            return StaffClientVisibility::restrictAdminsQueryForStaff($query, $user);
        }

        return $query;
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>
     */
    protected function withClientAccessFlags(array $item, Admin $client): array
    {
        $user = Auth::guard('admin')->user();
        $adminId = (int) $client->id;
        $item['admin_id'] = $adminId;
        $item['is_lead'] = (($client->type ?? '') === 'lead');
        if (! $user instanceof Staff) {
            $item['locked'] = false;
            $item['access_ui'] = ['show_quick' => false, 'show_supervisor' => false];
            $item['requires_access_grant'] = false;
            $item['has_active_temp_access'] = false;
            $item['allow_access_modal'] = false;
            $item['search_selection_requires_access_modal'] = false;

            return $item;
        }

        $canAccess = StaffClientVisibility::canAccessAdminRecord($adminId, $user);
        $item['locked'] = ! $canAccess;
        $item['access_ui'] = $item['locked']
            ? StaffClientVisibility::crossAccessUiFlags($user)
            : ['show_quick' => false, 'show_supervisor' => false];
        $item['requires_access_grant'] = $item['locked']
            && StaffClientVisibility::staffMayOpenCrossAccessRequest($user, $adminId);
        $item['has_active_temp_access'] = app(CrmAccessService::class)->hasActiveGrant($user, $adminId);
        $item['allow_access_modal'] = true;

        // Staff with Quick access enabled but no Clients module (e.g. BI): may appear in search via
        // allocation but should not open full detail directly from global search — same as Quick view row.
        $noClientsModule = ! StaffClientVisibility::staffHasClientsModule($user);
        $item['search_selection_requires_access_modal'] = ! $item['has_active_temp_access']
            && ! $item['locked']
            && $noClientsModule
            && (bool) ($user->quick_access_enabled ?? false);

        return $item;
    }

    /**
     * Perform the actual search
     */
    protected function performSearch()
    {
        $results = [];

        // Parse query for special searches
        $searchType = $this->detectSearchType(); //dd($searchType['type']);

        switch ($searchType['type']) {
            case 'client_id':
                $results = array_merge($results, $this->searchByClientId($searchType['value']));
                break;
            case 'email':
                $results = array_merge($results, $this->searchByEmail($searchType['value']));
                break;
            case 'phone':
                $results = array_merge($results, $this->searchByPhone($searchType['value']));
                break;
            default:
                // Search clients and leads (all in admins table)
                $results = $this->searchClients();
                break;
        }

        return ['items' => $results];
    }

    /**
     * Detect special search patterns
     */
    protected function detectSearchType()
    {
        // Check for client ID pattern with # prefix (e.g., #123)
        if (preg_match('/^#(\d+)$/', $this->query, $matches)) {
            return ['type' => 'client_id', 'value' => $matches[1]];
        }

        // Check for email pattern
        if (filter_var($this->query, FILTER_VALIDATE_EMAIL)) {
            return ['type' => 'email', 'value' => $this->query];
        }

        // Check for phone pattern (contains only digits and common separators)
        if (preg_match('/^[\d\s\-\+\(\)]+$/', $this->query)) {
            $digitsOnly = preg_replace('/[^\d]/', '', $this->query);
            
            // Normalize Australian mobile numbers
            $normalizedPhone = $this->normalizeAustralianPhone($digitsOnly);
            
            // Treat as phone if:
            // - Starts with 4 (Australian mobile after normalization)
            // - Or has 7+ digits (standard phone length)
            if (preg_match('/^4/', $normalizedPhone) || strlen($digitsOnly) >= 7) {
                return ['type' => 'phone', 'value' => $normalizedPhone];
            } else {
                // Short numbers (1-6 digits) not starting with 4 are client IDs
                return ['type' => 'client_id', 'value' => $digitsOnly];
            }
        }

        return ['type' => 'general', 'value' => $this->query];
    }

    /**
     * Search clients
     */
    protected function searchClients()
    {
        $query = $this->query;
        $dob = $this->parseDOB($query);

        $phoneSubquery = DB::table('client_phones')
            ->select('client_id', DB::raw("STRING_AGG(client_phone, ', ') as phones"))
            ->groupBy('client_id');

        $clients = Admin::where('admins.is_archived', '=', 0)
            ->where(function ($q) {
                $q->whereNull('admins.is_deleted')
                  ->orWhere('admins.is_deleted', 0);
            })
            /*->where(function ($q) {
                $q->whereNull('admins.lead_id')
                  ->orWhere('admins.lead_id', 0)
                  ->orWhere('admins.lead_id', '');
            })*/
            ->leftJoinSub($phoneSubquery, 'phone_data', 'admins.id', '=', 'phone_data.client_id')
            ->where(function ($q) use ($query, $dob) {
                $q->where('admins.email', 'ilike', '%' . $query . '%')
                  ->orWhere('admins.first_name', 'ilike', '%' . $query . '%')
                  ->orWhere('admins.last_name', 'ilike', '%' . $query . '%')
                  ->orWhere('admins.client_id', 'ilike', '%' . $query . '%')
                  ->orWhere('admins.phone', 'ilike', '%' . $query . '%')
                  ->orWhere(DB::raw("COALESCE(admins.first_name, '') || ' ' || COALESCE(admins.last_name, '')"), 'ilike', '%' . $query . '%')
                  ->orWhere('phone_data.phones', 'ilike', '%' . $query . '%');
                
                if ($dob) {
                    $q->orWhere('admins.dob', '=', $dob);
                }
            });
        $clients = $this->applyStaffVisibilityToAdminQuery($clients)
            ->select('admins.*', 'phone_data.phones')
            ->limit($this->limit)
            ->get();

        return $clients->map(function ($client) {
            if (empty($client->id)) {
                return null; // Skip records without ID
            }
            
            $displayType = ($client->type ?? '') == 'lead' ? 'Lead' : 'Client';
            $isArchived = ($client->is_archived ?? 0) == 1;
            $badgeColor = $isArchived ? 'gray' : (($client->type ?? '') == 'lead' ? 'blue' : 'yellow');
            
            $firstName = $client->first_name ?? '';
            $lastName = $client->last_name ?? '';
            $fullName = trim($firstName . ' ' . $lastName);
            
            return $this->withClientAccessFlags([
                'name' => $this->highlightMatch($fullName ?: 'Unknown'),
                'email' => $this->highlightMatch($client->email ?? ''),
                'phone' => $this->highlightMatch($client->phone ?? ''),
                'client_id' => $client->client_id ?? null,
                'status' => $isArchived ? 'Archived' : $displayType,
                'type' => 'Client', // Always route to client detail page
                'id' => $this->encodeId($client->id) . '/Client',
                'raw_id' => $client->id,
                'category' => 'clients',
                'badge_color' => $badgeColor
            ], $client);
        })->filter()->values()->toArray();
    }

    /**
     * Search by specific client ID
     */
    protected function searchByClientId($clientId)
    {
        $clients = Admin::where('is_archived', '=', 0)
            ->where(function ($q) {
                $q->whereNull('is_deleted')->orWhere('is_deleted', 0);
            })
            /*->where(function ($q) {
                $q->whereNull('lead_id')
                  ->orWhere('lead_id', 0)
                  ->orWhere('lead_id', '');
            })*/
            ->where(function ($q) use ($clientId) {
                $q->where('client_id', 'ilike', '%' . $clientId . '%')
                  ->orWhere('id', '=', $clientId);
            });
        $clients = $this->applyStaffVisibilityToAdminQuery($clients)
            ->limit(10)
            ->get();

        return $clients->map(function ($client) {
            $displayType = $client->type == 'lead' ? 'Lead' : 'Client';
            $badgeColor = $client->is_archived == 1 ? 'gray' : ($client->type == 'lead' ? 'blue' : 'yellow');
            
            return $this->withClientAccessFlags([
                'name' => $client->first_name . ' ' . $client->last_name,
                'email' => $client->email ?? '',
                'phone' => $client->phone ?? '',
                'client_id' => $client->client_id,
                'status' => $client->is_archived == 1 ? 'Archived' : $displayType,
                'type' => 'Client',
                'id' => base64_encode(convert_uuencode($client->id)) . '/Client',
                'raw_id' => $client->id,
                'category' => 'clients',
                'badge_color' => $badgeColor
            ], $client);
        })->toArray();
    }

    /**
     * Search by email
     */
    protected function searchByEmail($email)
    {
        $results = [];

        // Search clients (exclude archived)
        $clients = Admin::where('is_archived', '=', 0)
            ->where(function ($q) {
                $q->whereNull('is_deleted')->orWhere('is_deleted', 0);
            })
            /*->where(function ($q) {
                $q->whereNull('lead_id')
                  ->orWhere('lead_id', 0)
                  ->orWhere('lead_id', '');
            })*/
            ->where(function ($q) use ($email) {
                $q->where('email', 'ilike', '%' . $email . '%');
            });
        $clients = $this->applyStaffVisibilityToAdminQuery($clients)
            ->limit(10)
            ->get();

        foreach ($clients as $client) {
            $displayType = $client->type == 'lead' ? 'Lead' : 'Client';
            $badgeColor = $client->type == 'lead' ? 'blue' : 'yellow';
            
            $results[] = $this->withClientAccessFlags([
                'name' => $client->first_name . ' ' . $client->last_name,
                'email' => $client->email ?? '',
                'phone' => $client->phone ?? '',
                'status' => $displayType,
                'type' => 'Client',
                'id' => base64_encode(convert_uuencode($client->id)) . '/Client',
                'raw_id' => $client->id,
                'category' => 'clients',
                'badge_color' => $badgeColor
            ], $client);
        }

        return $results;
    }

    /**
     * Search by phone
     * Handles Australian mobile variations: 04xx, 614xx, 4xx
     */
    protected function searchByPhone($phone)
    {
        $results = [];
        
        // Normalize the search phone (e.g., 0412345678 or 61412345678 → 412345678)
        $corePhone = $this->normalizeAustralianPhone($phone);
        
        // Build search patterns to match all possible formats
        // For 412345678, we want to match: 0412345678, 61412345678, 412345678, +61412345678
        $searchPatterns = [
            '%' . $corePhone . '%',        // Matches: 412345678, 61412345678, etc.
            '%0' . $corePhone . '%',       // Explicitly match with leading 0
            '%61' . $corePhone . '%'       // Explicitly match with country code
        ];

        $phoneSubquery = DB::table('client_phones')
            ->select('client_id', DB::raw("STRING_AGG(client_phone, ', ') as phones"))
            ->groupBy('client_id');

        // Search clients (exclude archived)
        $clients = Admin::where('admins.is_archived', '=', 0)
            ->where(function ($q) {
                $q->whereNull('admins.is_deleted')->orWhere('admins.is_deleted', 0);
            })
            /*->where(function ($q) {
                $q->whereNull('admins.lead_id')
                  ->orWhere('admins.lead_id', 0)
                  ->orWhere('admins.lead_id', '');
            })*/
            ->leftJoinSub($phoneSubquery, 'phone_data', 'admins.id', '=', 'phone_data.client_id')
            ->where(function ($q) use ($searchPatterns) {
                foreach ($searchPatterns as $pattern) {
                    $q->orWhere('admins.phone', 'ilike', $pattern)
                      ->orWhere('phone_data.phones', 'ilike', $pattern);
                }
            });
        $clients = $this->applyStaffVisibilityToAdminQuery($clients)
            ->select('admins.*')
            ->distinct()
            ->limit(10)
            ->get();

        foreach ($clients as $client) {
            $displayType = $client->type == 'lead' ? 'Lead' : 'Client';
            $badgeColor = $client->type == 'lead' ? 'blue' : 'yellow';
            
            $results[] = $this->withClientAccessFlags([
                'name' => $client->first_name . ' ' . $client->last_name,
                'email' => $client->email ?? '',
                'phone' => $client->phone ?? '',
                'status' => $displayType,
                'type' => 'Client',
                'id' => base64_encode(convert_uuencode($client->id)) . '/Client',
                'raw_id' => $client->id,
                'category' => 'clients',
                'badge_color' => $badgeColor
            ], $client);
        }

        return $results;
    }

    /**
     * Normalize Australian phone number to core digits
     * Removes country code (61), leading zero, and formatting
     * 
     * Examples:
     * - 0412345678 → 412345678
     * - 61412345678 → 412345678
     * - +61412345678 → 412345678
     * - 412345678 → 412345678
     */
    protected function normalizeAustralianPhone($phone)
    {
        // Strip all non-digits
        $digitsOnly = preg_replace('/[^\d]/', '', $phone);
        
        // Remove country code (61) if it's at the start and followed by 4
        if (preg_match('/^61(4\d+)$/', $digitsOnly, $matches)) {
            return $matches[1]; // Returns 412345678
        }
        
        // Remove leading 0 if followed by 4
        if (preg_match('/^0(4\d+)$/', $digitsOnly, $matches)) {
            return $matches[1]; // Returns 412345678
        }
        
        // Already in correct format (starts with 4)
        if (preg_match('/^4\d+$/', $digitsOnly)) {
            return $digitsOnly; // Returns 412345678
        }
        
        // Not an Australian mobile, return as-is for other phone types
        return $digitsOnly;
    }

    /**
     * Parse DOB from query
     */
    protected function parseDOB($query)
    {
        if (strstr($query, '/')) {
            $dob = explode('/', $query);
            if (!empty($dob) && is_array($dob) && count($dob) == 3) {
                return $dob[2] . '/' . $dob[1] . '/' . $dob[0];
            }
        }
        return null;
    }

    /**
     * Highlight matching text
     */
    protected function highlightMatch($text)
    {
        if (empty($text)) {
            return $text;
        }

        $query = preg_quote($this->query, '/');
        return preg_replace(
            '/(' . $query . ')/i',
            '<mark class="search-highlight">$1</mark>',
            $text
        );
    }

    /**
     * Empty response
     */
    protected function emptyResponse()
    {
        return ['items' => []];
    }
}

