<?php

namespace App\Services;

use App\Models\Note;
// use App\Models\Task; // Task system removed - December 2025
use App\Models\CheckinLog;
use App\Models\Contact;
use App\Models\Partner;
use App\Models\Admin;
use App\Models\UserLog;
use App\Models\ActivitiesLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class DashboardService
{
    /**
     * Get today's action count
     *
     * @return int
     */
    public function getTodayActionCount()
    {
        try {
            // Use whereBetween instead of whereDate for better index usage
            $startOfDay = Carbon::today()->startOfDay();
            $endOfDay = Carbon::today()->endOfDay();
            
            if (Auth::user()->role == 1) {
                return Note::whereBetween('followup_date', [$startOfDay, $endOfDay])->count();
            } else {
                return Note::whereBetween('followup_date', [$startOfDay, $endOfDay])
                    ->where('assigned_to', Auth::user()->id)
                    ->count();
            }
        } catch (\Exception $e) {
            \Log::error('Error getting today action count: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get actions (Notes) assigned to the user
     * 
     * For super admin (role == 1), shows all actions.
     * For other users, shows only actions assigned to them.
     * 
     * @param string $dateFilter Optional date filter (today, week, month)
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTodayTasks($dateFilter = 'today')
    {
        try {
            $user = Auth::user();
            if (!$user) {
                \Log::error('No authenticated user found in getTodayTasks');
                return collect([]);
            }

            $query = Note::with(['noteUser', 'noteClient', 'assigned_user'])
                ->where('status', '<>', 1) // Not completed
                ->whereIn('type', ['client', 'partner'])
                ->where('folloup', 1) // Active action
                ->whereNotNull('followup_date');

            // Filter by assigned user (unless super admin - role == 1)
            // Super admin sees all actions (including unassigned)
            // Regular users see only actions assigned to them (assigned_to must match their ID)
            if ($user->role != 1) {
                $query->where(function($q) use ($user) {
                    $q->where('assigned_to', $user->id)
                      ->whereNotNull('assigned_to'); // Ensure assigned_to is not null
                });
            }
            // For super admin (role == 1), no additional filter - shows all actions

            // Apply date filter based on action date (followup_date column)
            // Note: Removed strict date filtering to show all pending actions
            // If you want to filter by date, uncomment the switch statement below
            switch ($dateFilter) {
                case 'week':
                    $startDate = Carbon::now()->startOfWeek();
                    $endDate = Carbon::now()->endOfWeek();
                    $query->whereBetween('followup_date', [$startDate, $endDate]);
                    break;
                case 'month':
                    $startDate = Carbon::now()->startOfMonth();
                    $endDate = Carbon::now()->endOfMonth();
                    $query->whereBetween('followup_date', [$startDate, $endDate]);
                    break;
                case 'today':
                default:
                    // Show all pending actions regardless of date
                    // This includes overdue, today, and future actions
                    // No date filter applied - shows all actions with followup_date set
                    break;
            }

            $actions = $query->orderBy('followup_date', 'ASC')
                ->limit(50) // Limit to 50 actions for performance
                ->get();

            // Get partner IDs for partner-type actions
            $partnerIds = $actions->where('type', 'partner')->pluck('client_id')->unique()->filter();
            
            // Load partners in bulk to avoid N+1 queries
            $partners = [];
            if ($partnerIds->count() > 0) {
                $partnerModels = Partner::whereIn('id', $partnerIds)->get()->keyBy('id');
                $partners = $partnerModels->toArray();
            }

            // Format dates and add partner/client name for display
            $actions->transform(function($action) use ($partners) {
                $action->formatted_due_date = $action->followup_date 
                    ? Carbon::parse($action->followup_date)->format('d/m/Y h:i A') 
                    : 'N/A';
                
                // Add user relationship alias for backward compatibility with view
                $action->user = $action->assigned_user;
                
                // Add client/partner name for easy access in view
                if ($action->type == 'client' && $action->noteClient) {
                    $action->client_name = trim(($action->noteClient->first_name ?? '') . ' ' . ($action->noteClient->last_name ?? ''));
                } elseif ($action->type == 'partner' && isset($partners[$action->client_id])) {
                    $action->client_name = $partners[$action->client_id]['partner_name'] ?? 'N/A';
                } else {
                    $action->client_name = 'N/A';
                }
                
                return $action;
            });

            return $actions;
        } catch (\Exception $e) {
            \Log::error('Error getting actions: ' . $e->getMessage());
            \Log::error('Error trace: ' . $e->getTraceAsString());
            return collect([]);
        }
    }

    /**
     * Get check-in queue data
     *
     * @return array
     */
    public function getCheckInQueue()
    {
        try {
            // Remove unnecessary condition and eager load client relationship
            $checkins = CheckinLog::where('status', 0)
                ->select('id', 'client_id', 'created_at')
                ->with(['client' => function($q) {
                    $q->where('role', 7)->select('id', 'first_name', 'last_name');
                }])
                ->orderBy('created_at', 'ASC')
                ->limit(20) // Limit to 20 check-ins for performance
                ->get();

            // Pre-format dates to avoid date() calls in view
            $checkins->transform(function($checkin) {
                $checkin->formatted_waiting_time = Carbon::parse($checkin->created_at)->format('h:i A');
                return $checkin;
            });

            $totalData = $checkins->count();

            return [
                'total' => $totalData,
                'items' => $checkins
            ];
        } catch (\Exception $e) {
            \Log::error('Error getting check-in queue: ' . $e->getMessage());
            return [
                'total' => 0,
                'items' => collect([])
            ];
        }
    }

    /**
     * Get notes with deadlines
     *
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getNotesWithDeadlines($perPage = null)
    {
        try {
            $perPage = $perPage ?? config('constants.limit', 10);

            // Use join to avoid N+1 queries for Partner data
            $query = DB::table('notes')
                ->leftJoin('partners', 'notes.client_id', '=', 'partners.id')
                ->whereNotNull('notes.note_deadline')
                ->where('notes.status', '!=', 1)
                ->select(
                    'notes.id',
                    'notes.client_id',
                    'notes.description',
                    'notes.note_deadline',
                    'notes.created_at',
                    'notes.status',
                    'partners.partner_name'
                );

            if (Auth::user()->role != 1) {
                $query->where('notes.assigned_to', Auth::user()->id);
            }

            $notes = $query->orderBy('notes.note_deadline', 'DESC')
                ->paginate($perPage);

            // Pre-format dates to avoid date() calls in view
            $notes->getCollection()->transform(function($note) {
                $note->formatted_deadline = $note->note_deadline ? Carbon::parse($note->note_deadline)->format('d/m/Y') : 'N/A';
                $note->formatted_created_at = $note->created_at ? Carbon::parse($note->created_at)->format('d/m/Y') : 'N/A';
                return $note;
            });

            return $notes;
        } catch (\Exception $e) {
            \Log::error('Error getting notes with deadlines: ' . $e->getMessage());
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, $perPage);
        }
    }

    /**
     * Get login statistics for the current user
     *
     * @return array
     */
    public function getLoginStatistics()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return $this->getEmptyLoginStats();
            }

            // Get current login (most recent "Logged in successfully" entry)
            $currentLoginLog = UserLog::where('user_id', $user->id)
                ->where('message', 'Logged in successfully')
                ->orderBy('created_at', 'DESC')
                ->first();

            // Get previous login (second most recent "Logged in successfully" entry)
            $lastLoginLog = UserLog::where('user_id', $user->id)
                ->where('message', 'Logged in successfully')
                ->orderBy('created_at', 'DESC')
                ->skip(1)
                ->first();

            $currentLoginTime = $currentLoginLog ? Carbon::parse($currentLoginLog->created_at) : null;
            $lastLoginTime = $lastLoginLog ? Carbon::parse($lastLoginLog->created_at) : null;

            // Get current session activity from sessions table
            $sessionId = Session::getId();
            $currentSession = DB::table('sessions')
                ->where('id', $sessionId)
                ->first();

            $currentActivityTime = $currentSession ? Carbon::createFromTimestamp($currentSession->last_activity) : Carbon::now();

            // Calculate time since last login
            $timeSinceLastLogin = null;
            $timeSinceLastLoginFormatted = 'Never';
            if ($lastLoginTime) {
                $timeSinceLastLogin = Carbon::now()->diffInSeconds($lastLoginTime);
                $timeSinceLastLoginFormatted = $this->formatTimeDifference($lastLoginTime);
            }

            // Calculate current session duration (time since current login)
            $currentSessionDuration = $currentLoginTime ? Carbon::now()->diffInSeconds($currentLoginTime) : 0;
            $currentSessionDurationFormatted = $this->formatDuration($currentSessionDuration);

            // Calculate inactivity period (if last activity was more than 5 minutes ago)
            $inactivityPeriod = null;
            $inactivityFormatted = 'Active';
            $inactivitySeconds = Carbon::now()->diffInSeconds($currentActivityTime);
            
            if ($inactivitySeconds > 300) { // More than 5 minutes
                $inactivityPeriod = $inactivitySeconds;
                $inactivityFormatted = $this->formatTimeDifference($currentActivityTime);
            }

            return [
                'last_login_time' => $lastLoginTime,
                'last_login_formatted' => $lastLoginTime ? $lastLoginTime->format('d/m/Y h:i A') : 'Never',
                'time_since_last_login' => $timeSinceLastLogin,
                'time_since_last_login_formatted' => $timeSinceLastLoginFormatted,
                'current_login_time' => $currentLoginTime,
                'current_login_formatted' => $currentLoginTime ? $currentLoginTime->format('d/m/Y h:i A') : 'Never',
                'current_activity_time' => $currentActivityTime,
                'current_activity_formatted' => $currentActivityTime->format('d/m/Y h:i A'),
                'current_session_duration' => $currentSessionDuration,
                'current_session_duration_formatted' => $currentSessionDurationFormatted,
                'inactivity_period' => $inactivityPeriod,
                'inactivity_formatted' => $inactivityFormatted,
                'is_active' => $inactivitySeconds <= 300, // Active if last activity within 5 minutes
                'last_login_ip' => $lastLoginLog ? $lastLoginLog->ip_address : null,
                'current_login_ip' => $currentLoginLog ? $currentLoginLog->ip_address : null,
            ];
        } catch (\Exception $e) {
            \Log::error('Error getting login statistics: ' . $e->getMessage());
            return $this->getEmptyLoginStats();
        }
    }

    /**
     * Format time difference in human readable format
     *
     * @param Carbon $time
     * @return string
     */
    private function formatTimeDifference($time)
    {
        if (!$time) {
            return 'Never';
        }

        $diff = Carbon::now()->diff($time);
        
        if ($diff->days > 0) {
            return $diff->days . ' day' . ($diff->days > 1 ? 's' : '') . ' ago';
        } elseif ($diff->h > 0) {
            return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
        } elseif ($diff->i > 0) {
            return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
        } else {
            return 'Just now';
        }
    }

    /**
     * Format duration in seconds to human readable format
     *
     * @param int $seconds
     * @return string
     */
    private function formatDuration($seconds)
    {
        if ($seconds < 60) {
            return $seconds . ' second' . ($seconds != 1 ? 's' : '');
        } elseif ($seconds < 3600) {
            $minutes = floor($seconds / 60);
            return $minutes . ' minute' . ($minutes != 1 ? 's' : '');
        } elseif ($seconds < 86400) {
            $hours = floor($seconds / 3600);
            $minutes = floor(($seconds % 3600) / 60);
            $result = $hours . ' hour' . ($hours != 1 ? 's' : '');
            if ($minutes > 0) {
                $result .= ' ' . $minutes . ' minute' . ($minutes != 1 ? 's' : '');
            }
            return $result;
        } else {
            $days = floor($seconds / 86400);
            $hours = floor(($seconds % 86400) / 3600);
            $result = $days . ' day' . ($days != 1 ? 's' : '');
            if ($hours > 0) {
                $result .= ' ' . $hours . ' hour' . ($hours != 1 ? 's' : '');
            }
            return $result;
        }
    }

    /**
     * Get empty login statistics structure
     *
     * @return array
     */
    private function getEmptyLoginStats()
    {
        return [
            'last_login_time' => null,
            'last_login_formatted' => 'Never',
            'time_since_last_login' => null,
            'time_since_last_login_formatted' => 'Never',
            'current_login_time' => null,
            'current_login_formatted' => 'Never',
            'current_activity_time' => Carbon::now(),
            'current_activity_formatted' => Carbon::now()->format('d/m/Y h:i A'),
            'current_session_duration' => 0,
            'current_session_duration_formatted' => '0 seconds',
            'inactivity_period' => null,
            'inactivity_formatted' => 'Active',
            'is_active' => true,
            'last_login_ip' => null,
            'current_login_ip' => null,
        ];
    }

    /**
     * Get clients with recent activities
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getClientsWithRecentActivities($limit = 10)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return collect([]);
            }

            // Get recent activities (last 30 days to show more clients)
            $recentDate = Carbon::now()->subDays(30);
            
            $query = ActivitiesLog::with(['client' => function($q) {
                $q->select('id', 'first_name', 'last_name', 'email', 'phone', 'role');
            }])
            ->where('task_status', 0) // Only activities, not tasks
            ->whereNotNull('client_id')
            ->where('created_at', '>=', $recentDate)
            ->orderBy('created_at', 'DESC');

            // Filter by user role - super admin sees all, others see their own activities
            if ($user->role != 1) {
                $query->where('created_by', $user->id);
            }

            $activities = $query->get();

            // Get unique clients with their most recent activity
            $clientsWithActivities = $activities->groupBy('client_id')->map(function($clientActivities) {
                $mostRecent = $clientActivities->first();
                $client = $mostRecent->client;
                
                if (!$client) {
                    return null;
                }
                
                return (object)[
                    'client_id' => $client->id,
                    'client_role' => $client->role ?? null,
                    'client_name' => trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? '')),
                    'client_email' => $client->email ?? '',
                    'client_phone' => $client->phone ?? '',
                    'last_activity' => $mostRecent->created_at,
                    'last_activity_formatted' => Carbon::parse($mostRecent->created_at)->format('d/m/Y h:i A'),
                    'last_activity_time' => $this->formatActivityTime($mostRecent->created_at),
                    'activity_count' => $clientActivities->count(),
                    'last_activity_subject' => $mostRecent->subject ?? 'Activity',
                    'activity_type' => $this->extractActivityType($mostRecent->subject, $mostRecent->description)
                ];
            })->filter()->take($limit)->values();

            return $clientsWithActivities;
        } catch (\Exception $e) {
            \Log::error('Error getting clients with recent activities: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Get recent activities for the dashboard
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecentActivities($limit = 10)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return collect([]);
            }

            $query = ActivitiesLog::with(['client' => function($q) {
                $q->select('id', 'first_name', 'last_name');
            }, 'createdBy' => function($q) {
                $q->select('id', 'first_name', 'last_name');
            }])
            ->where('task_status', 0) // Only activities, not tasks
            ->orderBy('created_at', 'DESC')
            ->limit($limit);

            // Filter by user role - super admin sees all, others see their own activities
            if ($user->role != 1) {
                $query->where('created_by', $user->id);
            }

            $activities = $query->get();

            // Format activities for display
            $activities->transform(function($activity) {
                $activity->formatted_time = $this->formatActivityTime($activity->created_at);
                $activity->formatted_date = Carbon::parse($activity->created_at)->format('d/m/Y h:i A');
                
                // Extract activity type and details from subject/description
                $activity->activity_type = $this->extractActivityType($activity->subject, $activity->description);
                $activity->activity_details = $this->extractActivityDetails($activity->subject, $activity->description);
                
                return $activity;
            });

            return $activities;
        } catch (\Exception $e) {
            \Log::error('Error getting recent activities: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Format activity time for display
     *
     * @param string $datetime
     * @return string
     */
    private function formatActivityTime($datetime)
    {
        if (!$datetime) {
            return 'N/A';
        }

        $activityTime = Carbon::parse($datetime);
        $now = Carbon::now();
        
        if ($activityTime->isToday()) {
            return $activityTime->format('h:i A');
        } elseif ($activityTime->isYesterday()) {
            return 'Yesterday, ' . $activityTime->format('h:i A');
        } elseif ($activityTime->diffInDays($now) <= 7) {
            return $activityTime->format('D, h:i A');
        } else {
            return $activityTime->format('d/m/Y, h:i A');
        }
    }

    /**
     * Extract activity type from subject/description
     *
     * @param string $subject
     * @param string $description
     * @return string
     */
    private function extractActivityType($subject, $description)
    {
        $subjectLower = strtolower($subject ?? '');
        $descLower = strtolower($description ?? '');
        
        if (strpos($subjectLower, 'email') !== false || strpos($descLower, 'email sent') !== false) {
            return 'email';
        } elseif (strpos($subjectLower, 'file') !== false || strpos($descLower, 'uploaded') !== false || strpos($descLower, '.pdf') !== false || strpos($descLower, '.doc') !== false) {
            return 'file';
        } elseif (strpos($subjectLower, 'note') !== false || strpos($descLower, 'note added') !== false) {
            return 'note';
        } else {
            return 'activity';
        }
    }

    /**
     * Extract activity details for display
     *
     * @param string $subject
     * @param string $description
     * @return string
     */
    private function extractActivityDetails($subject, $description)
    {
        // Try to extract meaningful information from description
        if (!empty($description)) {
            // Remove HTML tags and get first meaningful sentence
            $cleanDesc = strip_tags($description);
            $cleanDesc = preg_replace('/\s+/', ' ', $cleanDesc);
            $cleanDesc = trim($cleanDesc);
            
            // Limit to reasonable length
            if (strlen($cleanDesc) > 100) {
                $cleanDesc = substr($cleanDesc, 0, 100) . '...';
            }
            
            if (!empty($cleanDesc)) {
                return $cleanDesc;
            }
        }
        
        // Fallback to subject
        return $subject ?? 'Activity';
    }

}

