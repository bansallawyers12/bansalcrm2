<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\Lead;
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
     * Main search method
     */
    public function search()
    {
        if (strlen($this->query) < 2) {
            return $this->emptyResponse();
        }

        $cacheKey = 'search:' . md5($this->query . ':' . $this->limit);

        if ($this->cacheEnabled) {
            return Cache::remember($cacheKey, 300, function () {
                return $this->performSearch();
            });
        }

        return $this->performSearch();
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
                // Search clients (including those with type='lead') and separate leads table
                $results = array_merge(
                    $this->searchClients(),
                    $this->searchLeads()
                );  //dd($results);
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

        $clients = Admin::where('admins.role', '=', 7)
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
                $q->where('admins.email', 'LIKE', '%' . $query . '%')
                  ->orWhere('admins.first_name', 'LIKE', '%' . $query . '%')
                  ->orWhere('admins.last_name', 'LIKE', '%' . $query . '%')
                  ->orWhere('admins.client_id', 'LIKE', '%' . $query . '%')
                  ->orWhere('admins.att_email', 'LIKE', '%' . $query . '%')
                  ->orWhere('admins.att_phone', 'LIKE', '%' . $query . '%')
                  ->orWhere('admins.phone', 'LIKE', '%' . $query . '%')
                  ->orWhere(DB::raw("CONCAT(admins.first_name, ' ', admins.last_name)"), 'LIKE', '%' . $query . '%')
                  ->orWhere('phone_data.phones', 'LIKE', '%' . $query . '%');
                
                if ($dob) {
                    $q->orWhere('admins.dob', '=', $dob);
                }
            })
            ->select('admins.*', '.phones')
            ->limit($this->limit)
            ->get();

        return $clients->map(function ($client) {
            $displayType = $client->type == 'lead' ? 'Lead' : 'Client';
            $badgeColor = $client->is_archived == 1 ? 'gray' : ($client->type == 'lead' ? 'blue' : 'yellow');
            
            return [
                'name' => $this->highlightMatch($client->first_name . ' ' . $client->last_name),
                'email' => $this->highlightMatch($client->email ?? ''),
                'phone' => $this->highlightMatch($client->phone ?? ''),
                'client_id' => $client->client_id,
                'status' => $client->is_archived == 1 ? 'Archived' : $displayType,
                'type' => 'Client', // Always route to client detail page
                'id' => base64_encode(convert_uuencode($client->id)) . '/Client',
                'raw_id' => $client->id,
                'category' => 'clients',
                'badge_color' => $badgeColor
            ];
        })->toArray();
    }

    /**
     * Search leads
     * Excludes leads that already exist in admins table (prioritize clients over leads)
     */
    protected function searchLeads()
    {
        $query = $this->query;
        $dob = $this->parseDOB($query);

        $leads = Lead::where('converted', '=', 0)
            // Exclude leads that already exist in admins table (via lead_id)
            ->whereNotIn('id', function($subquery) {
                $subquery->select('lead_id')
                    ->from('admins')
                    ->where('role', 7)
                    ->whereNotNull('lead_id')
                    ->where('lead_id', '!=', 0)
                    ->where('lead_id', '!=', '');
            })
            // Also exclude by email match - if email exists in admins, don't show in leads
            ->whereNotExists(function($subquery) {
                $subquery->select(DB::raw(1))
                    ->from('admins')
                    ->where('role', 7)
                    ->where(function($q) {
                        // Match leads.email with admins.email or admins.att_email
                        $q->where(function($q1) {
                            $q1->whereColumn('admins.email', 'leads.email')
                               ->whereNotNull('admins.email')
                               ->where('admins.email', '!=', '')
                               ->whereNotNull('leads.email')
                               ->where('leads.email', '!=', '');
                        })
                        ->orWhere(function($q2) {
                            $q2->whereColumn('admins.att_email', 'leads.email')
                               ->whereNotNull('admins.att_email')
                               ->where('admins.att_email', '!=', '')
                               ->whereNotNull('leads.email')
                               ->where('leads.email', '!=', '');
                        })
                        // Match leads.att_email with admins.email or admins.att_email
                        ->orWhere(function($q3) {
                            $q3->whereColumn('admins.email', 'leads.att_email')
                               ->whereNotNull('admins.email')
                               ->where('admins.email', '!=', '')
                               ->whereNotNull('leads.att_email')
                               ->where('leads.att_email', '!=', '');
                        })
                        ->orWhere(function($q4) {
                            $q4->whereColumn('admins.att_email', 'leads.att_email')
                               ->whereNotNull('admins.att_email')
                               ->where('admins.att_email', '!=', '')
                               ->whereNotNull('leads.att_email')
                               ->where('leads.att_email', '!=', '');
                        });
                    });
            })
            ->where(function ($q) use ($query, $dob) {
                $q->where('email', 'LIKE', '%' . $query . '%')
                  ->orWhere('first_name', 'LIKE', '%' . $query . '%')
                  ->orWhere('last_name', 'LIKE', '%' . $query . '%')
                  ->orWhere('phone', 'LIKE', '%' . $query . '%')
                  ->orWhere(DB::raw("CONCAT(first_name, ' ', last_name)"), 'LIKE', '%' . $query . '%');
                
                if ($dob) {
                    $q->orWhere('dob', '=', $dob);
                }
            })
            ->limit($this->limit)
            ->get();

        return $leads->map(function ($lead) {
            return [
                'name' => $this->highlightMatch($lead->first_name . ' ' . $lead->last_name),
                'email' => $this->highlightMatch($lead->email ?? ''),
                'phone' => $this->highlightMatch($lead->phone ?? ''),
                'client_id' => null,
                'status' => 'Lead',
                'type' => 'Lead',
                'id' => base64_encode(convert_uuencode($lead->id)) . '/Lead',
                'raw_id' => $lead->id,
                'category' => 'leads',
                'badge_color' => 'blue'
            ];
        })->toArray();
    }


    /**
     * Search by specific client ID
     */
    protected function searchByClientId($clientId)
    {
        $clients = Admin::where('role', '=', 7)
            ->where(function ($q) {
                $q->whereNull('is_deleted')->orWhere('is_deleted', 0);
            })
            /*->where(function ($q) {
                $q->whereNull('lead_id')
                  ->orWhere('lead_id', 0)
                  ->orWhere('lead_id', '');
            })*/
            ->where(function ($q) use ($clientId) {
                $q->where('client_id', 'LIKE', '%' . $clientId . '%')
                  ->orWhere('id', '=', $clientId);
            })
            ->limit(10)
            ->get();

        return $clients->map(function ($client) {
            $displayType = $client->type == 'lead' ? 'Lead' : 'Client';
            $badgeColor = $client->is_archived == 1 ? 'gray' : ($client->type == 'lead' ? 'blue' : 'yellow');
            
            return [
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
            ];
        })->toArray();
    }

    /**
     * Search by email
     */
    protected function searchByEmail($email)
    {
        $results = [];

        // Search clients
        $clients = Admin::where('role', '=', 7)
            ->where(function ($q) {
                $q->whereNull('is_deleted')->orWhere('is_deleted', 0);
            })
            /*->where(function ($q) {
                $q->whereNull('lead_id')
                  ->orWhere('lead_id', 0)
                  ->orWhere('lead_id', '');
            })*/
            ->where(function ($q) use ($email) {
                $q->where('email', 'LIKE', '%' . $email . '%')
                  ->orWhere('att_email', 'LIKE', '%' . $email . '%');
            })
            ->limit(10)
            ->get();

        foreach ($clients as $client) {
            $displayType = $client->type == 'lead' ? 'Lead' : 'Client';
            $badgeColor = $client->type == 'lead' ? 'blue' : 'yellow';
            
            $results[] = [
                'name' => $client->first_name . ' ' . $client->last_name,
                'email' => $client->email ?? '',
                'phone' => $client->phone ?? '',
                'status' => $displayType,
                'type' => 'Client',
                'id' => base64_encode(convert_uuencode($client->id)) . '/Client',
                'category' => 'clients',
                'badge_color' => $badgeColor
            ];
        }

        // Search leads
        // Exclude leads that already exist in admins table (prioritize clients over leads)
        // Check both by lead_id and by email match to catch all cases
        $leads = Lead::where('converted', '=', 0)
            ->whereNotIn('id', function($subquery) {
                // Exclude by lead_id if it exists in admins
                $subquery->select('lead_id')
                    ->from('admins')
                    ->where('role', 7)
                    ->whereNotNull('lead_id')
                    ->where('lead_id', '!=', 0)
                    ->where('lead_id', '!=', '');
            })
            // Also exclude by email match - if email exists in admins, don't show in leads
            ->whereNotExists(function($subquery) {
                $subquery->select(DB::raw(1))
                    ->from('admins')
                    ->where('role', 7)
                    ->where(function($q) {
                        // Match leads.email with admins.email or admins.att_email
                        $q->where(function($q1) {
                            $q1->whereColumn('admins.email', 'leads.email')
                               ->whereNotNull('admins.email')
                               ->where('admins.email', '!=', '')
                               ->whereNotNull('leads.email')
                               ->where('leads.email', '!=', '');
                        })
                        ->orWhere(function($q2) {
                            $q2->whereColumn('admins.att_email', 'leads.email')
                               ->whereNotNull('admins.att_email')
                               ->where('admins.att_email', '!=', '')
                               ->whereNotNull('leads.email')
                               ->where('leads.email', '!=', '');
                        })
                        // Match leads.att_email with admins.email or admins.att_email
                        ->orWhere(function($q3) {
                            $q3->whereColumn('admins.email', 'leads.att_email')
                               ->whereNotNull('admins.email')
                               ->where('admins.email', '!=', '')
                               ->whereNotNull('leads.att_email')
                               ->where('leads.att_email', '!=', '');
                        })
                        ->orWhere(function($q4) {
                            $q4->whereColumn('admins.att_email', 'leads.att_email')
                               ->whereNotNull('admins.att_email')
                               ->where('admins.att_email', '!=', '')
                               ->whereNotNull('leads.att_email')
                               ->where('leads.att_email', '!=', '');
                        });
                    });
            })
            ->where(function ($q) use ($email) {
                $q->where('email', 'LIKE', '%' . $email . '%')
                  ->orWhere('att_email', 'LIKE', '%' . $email . '%');
            })
            ->limit(10)
            ->get();

        foreach ($leads as $lead) {
            $results[] = [
                'name' => $lead->first_name . ' ' . $lead->last_name,
                'email' => $lead->email ?? '',
                'phone' => $lead->phone ?? '',
                'status' => 'Lead',
                'type' => 'Lead',
                'id' => base64_encode(convert_uuencode($lead->id)) . '/Lead',
                'category' => 'leads',
                'badge_color' => 'blue'
            ];
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

        // Search clients
        $clients = Admin::where('admins.role', '=', 7)
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
                    $q->orWhere('admins.phone', 'LIKE', $pattern)
                      ->orWhere('admins.att_phone', 'LIKE', $pattern)
                      ->orWhere('phone_data.phones', 'LIKE', $pattern);
                }
            })
            ->select('admins.*')
            ->distinct()
            ->limit(10)
            ->get();

        foreach ($clients as $client) {
            $displayType = $client->type == 'lead' ? 'Lead' : 'Client';
            $badgeColor = $client->type == 'lead' ? 'blue' : 'yellow';
            
            $results[] = [
                'name' => $client->first_name . ' ' . $client->last_name,
                'email' => $client->email ?? '',
                'phone' => $client->phone ?? '',
                'status' => $displayType,
                'type' => 'Client',
                'id' => base64_encode(convert_uuencode($client->id)) . '/Client',
                'category' => 'clients',
                'badge_color' => $badgeColor
            ];
        }

        // Search leads
        // Exclude leads that already exist in admins table (prioritize clients over leads)
        $leads = Lead::where('converted', '=', 0)
            ->whereNotIn('id', function($subquery) {
                $subquery->select('lead_id')
                    ->from('admins')
                    ->where('role', 7)
                    ->whereNotNull('lead_id')
                    ->where('lead_id', '!=', 0)
                    ->where('lead_id', '!=', '');
            })
            // Also exclude by email match - if email exists in admins, don't show in leads
            ->whereNotExists(function($subquery) {
                $subquery->select(DB::raw(1))
                    ->from('admins')
                    ->where('role', 7)
                    ->where(function($q) {
                        // Match leads.email with admins.email or admins.att_email
                        $q->where(function($q1) {
                            $q1->whereColumn('admins.email', 'leads.email')
                               ->whereNotNull('admins.email')
                               ->where('admins.email', '!=', '')
                               ->whereNotNull('leads.email')
                               ->where('leads.email', '!=', '');
                        })
                        ->orWhere(function($q2) {
                            $q2->whereColumn('admins.att_email', 'leads.email')
                               ->whereNotNull('admins.att_email')
                               ->where('admins.att_email', '!=', '')
                               ->whereNotNull('leads.email')
                               ->where('leads.email', '!=', '');
                        })
                        // Match leads.att_email with admins.email or admins.att_email
                        ->orWhere(function($q3) {
                            $q3->whereColumn('admins.email', 'leads.att_email')
                               ->whereNotNull('admins.email')
                               ->where('admins.email', '!=', '')
                               ->whereNotNull('leads.att_email')
                               ->where('leads.att_email', '!=', '');
                        })
                        ->orWhere(function($q4) {
                            $q4->whereColumn('admins.att_email', 'leads.att_email')
                               ->whereNotNull('admins.att_email')
                               ->where('admins.att_email', '!=', '')
                               ->whereNotNull('leads.att_email')
                               ->where('leads.att_email', '!=', '');
                        });
                    });
            })
            ->where(function ($q) use ($searchPatterns) {
                foreach ($searchPatterns as $pattern) {
                    $q->orWhere('phone', 'LIKE', $pattern)
                      ->orWhere('att_phone', 'LIKE', $pattern);
                }
            })
            ->distinct()
            ->limit(10)
            ->get();

        foreach ($leads as $lead) {
            $results[] = [
                'name' => $lead->first_name . ' ' . $lead->last_name,
                'email' => $lead->email ?? '',
                'phone' => $lead->phone ?? '',
                'status' => 'Lead',
                'type' => 'Lead',
                'id' => base64_encode(convert_uuencode($lead->id)) . '/Lead',
                'category' => 'leads',
                'badge_color' => 'blue'
            ];
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

