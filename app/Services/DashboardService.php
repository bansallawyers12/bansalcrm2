<?php

namespace App\Services;

use App\Models\Note;
// use App\Models\Task; // Task system removed - December 2025
use App\Models\CheckinLog;
use App\Models\Contact;
use App\Models\Partner;
use App\Models\Admin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardService
{
    /**
     * Get today's followup count
     *
     * @return int
     */
    public function getTodayFollowupCount()
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
            \Log::error('Error getting today followup count: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get today's tasks for the user
     *
     * @param string $dateFilter Optional date filter (today, week, month)
     * @return \Illuminate\Database\Eloquent\Collection
     */
    // Task system removed - December 2025 (database tables preserved)
    public function getTodayTasks($dateFilter = 'today')
    {
        // Task system removed - returning empty collection
        return collect([]);
        /*
        try {
            $query = Task::query();
            
            // Apply date filter
            switch ($dateFilter) {
                case 'week':
                    $startDate = Carbon::now()->startOfWeek();
                    $endDate = Carbon::now()->endOfWeek();
                    $query->whereBetween('due_date', [$startDate, $endDate]);
                    break;
                case 'month':
                    $query->whereMonth('due_date', Carbon::now()->month)
                          ->whereYear('due_date', Carbon::now()->year);
                    break;
                case 'today':
                default:
                    // Use whereBetween for better index usage
                    $startOfDay = Carbon::today()->startOfDay();
                    $endOfDay = Carbon::today()->endOfDay();
                    $query->whereBetween('due_date', [$startOfDay, $endOfDay]);
                    break;
            }

            // Apply role-based filtering and eager load user relationship
            if (Auth::user()->role == 1) {
                $tasks = $query->select('id', 'user_id', 'status', 'due_date', 'due_time')
                    ->with(['user' => function($q) {
                        $q->select('id', 'first_name', 'last_name');
                    }])
                    ->orderBy('created_at', 'DESC')
                    ->limit(20) // Limit to 20 tasks for performance
                    ->get();
            } else {
                $tasks = $query->where(function($q) {
                    $q->where('assignee', Auth::user()->id)
                      ->orWhere('followers', Auth::user()->id);
                })
                ->select('id', 'user_id', 'status', 'due_date', 'due_time')
                ->with(['user' => function($q) {
                    $q->select('id', 'first_name', 'last_name');
                }])
                ->orderBy('created_at', 'DESC')
                ->limit(20) // Limit to 20 tasks for performance
                ->get();
            }

            // Pre-format dates to avoid date() calls in view
            $tasks->transform(function($task) {
                if ($task->due_date && $task->due_time) {
                    $task->formatted_due_date = Carbon::parse($task->due_date . ' ' . $task->due_time)
                        ->format('d/m/Y h:i A');
                } else {
                    $task->formatted_due_date = $task->due_date ? Carbon::parse($task->due_date)->format('d/m/Y') : 'N/A';
                }
                return $task;
            });

            return $tasks;
        } catch (\Exception $e) {
            \Log::error('Error getting today tasks: ' . $e->getMessage());
            return collect([]);
        }
        */
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

}

