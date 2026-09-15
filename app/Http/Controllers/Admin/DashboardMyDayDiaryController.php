<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Application;
use App\Models\Partner;
use App\Models\Staff;
use App\Models\StaffFileSession;
use App\Models\StaffFileTimeEntry;
use App\Services\StaffDayCrmEventsService;
use App\Services\StaffDayHoursService;
use App\Services\StaffFileSessionService;
use App\Services\StaffFileTimeService;
use App\Support\StaffClientVisibility;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class DashboardMyDayDiaryController extends Controller
{
    public function __construct(
        protected StaffDayCrmEventsService $crmEvents,
        protected StaffFileSessionService $fileSessions,
        protected StaffFileTimeService $fileTime,
        protected StaffDayHoursService $hours,
    ) {
        $this->middleware('auth:admin');
    }

    public function diary(): JsonResponse
    {
        $staff = $this->staffOrAbort();
        $staffId = (int) $staff->id;

        $events = $this->crmEvents->forStaff($staffId);
        $sessions = $this->fileSessions->sessionsForBoard($staffId);
        $manual = $this->fileTime->boardForStaff($staffId);
        $hours = $this->hours->forStaff($staffId);

        $eventMinutes = $sessions['event_minutes'] ?? [];
        $crmItems = array_map(function (array $item) use ($eventMinutes): array {
            $key = (string) ($item['key'] ?? '');
            $item['minutes'] = $eventMinutes[$key] ?? null;

            return $item;
        }, $events['items'] ?? []);

        return response()->json([
            'success' => true,
            'hours' => $hours,
            'crm_events' => [
                'items' => $crmItems,
                'total' => $events['total'] ?? 0,
                'more' => $events['more'] ?? 0,
                'date' => $events['date'] ?? null,
            ],
            'auto' => $sessions['auto'] ?? [],
            'opened' => $sessions['opened'] ?? [],
            'manual' => $manual['entries'] ?? [],
            'kinds' => StaffFileTimeEntry::kinds(),
        ]);
    }

    public function logCompleted(Request $request): JsonResponse
    {
        $staff = $this->staffOrAbort();
        $data = $request->validate([
            'kind' => ['required', Rule::in(StaffFileTimeEntry::kinds())],
            'title' => ['required', 'string', 'max:255'],
            'confirmed_minutes' => ['required', 'integer', 'min:1', 'max:480'],
            'admin' => ['sometimes', 'boolean'],
            'record_type' => ['nullable', Rule::in([StaffFileSession::RECORD_TYPE_STUDENT, StaffFileSession::RECORD_TYPE_PARTNER])],
            'record_id' => ['nullable', 'integer', 'min:1'],
            'application_id' => ['nullable', 'integer', 'min:1'],
        ]);

        $isAdmin = filter_var($data['admin'] ?? false, FILTER_VALIDATE_BOOLEAN);
        if (! $isAdmin && ($data['record_type'] ?? null) === StaffFileSession::RECORD_TYPE_STUDENT) {
            if (! StaffClientVisibility::canAccessAdminRecord((int) $data['record_id'])) {
                abort(403, 'Unauthorized');
            }
        }
        if (! $isAdmin && ($data['record_type'] ?? null) === StaffFileSession::RECORD_TYPE_PARTNER) {
            if (! Partner::query()->where('id', (int) $data['record_id'])->exists()) {
                abort(404, 'Partner not found.');
            }
        }

        $entry = $this->fileTime->logCompleted((int) $staff->id, $data);

        return response()->json([
            'success' => true,
            'entry' => $this->fileTime->serialize($entry),
        ]);
    }

    public function copySummary(): JsonResponse
    {
        $staff = $this->staffOrAbort();
        $summary = $this->fileTime->copySummary(
            (int) $staff->id,
            $this->crmEvents,
            $this->hours,
            $this->fileSessions,
        );

        return response()->json([
            'success' => true,
            'summary' => $summary,
        ]);
    }

    public function recordSearch(Request $request): JsonResponse
    {
        $this->staffOrAbort();
        $q = trim((string) $request->query('q', ''));
        if (strlen($q) < 2) {
            return response()->json(['success' => true, 'results' => []]);
        }

        $like = '%'.$q.'%';
        $results = [];

        $students = Admin::query()
            ->whereIn('type', ['client', 'lead'])
            ->where(function ($query) use ($like) {
                $query->where('client_id', 'like', $like)
                    ->orWhere('first_name', 'like', $like)
                    ->orWhere('last_name', 'like', $like)
                    ->orWhereRaw("CONCAT(COALESCE(first_name,''),' ',COALESCE(last_name,'')) like ?", [$like]);
            })
            ->orderByDesc('id')
            ->limit(8)
            ->get(['id', 'client_id', 'first_name', 'last_name', 'type']);

        foreach ($students as $student) {
            if (! StaffClientVisibility::canAccessAdminRecord((int) $student->id)) {
                continue;
            }
            $label = trim((string) ($student->client_id ?: ($student->first_name.' '.$student->last_name)));
            $results[] = [
                'record_type' => StaffFileSession::RECORD_TYPE_STUDENT,
                'record_id' => (int) $student->id,
                'application_id' => null,
                'label' => $label.' ('.($student->type ?? 'student').')',
            ];
        }

        $apps = Application::query()
            ->with(['client:id,first_name,last_name,client_id', 'partner:id,partner_name'])
            ->where(function ($query) use ($like, $q) {
                if (is_numeric($q)) {
                    $query->orWhere('id', (int) $q);
                }
                $query->orWhereHas('partner', fn ($p) => $p->where('partner_name', 'like', $like))
                    ->orWhereHas('client', function ($c) use ($like) {
                        $c->where('client_id', 'like', $like)
                            ->orWhere('first_name', 'like', $like)
                            ->orWhere('last_name', 'like', $like);
                    });
            })
            ->orderByDesc('id')
            ->limit(8)
            ->get(['id', 'client_id', 'partner_id', 'stage']);

        foreach ($apps as $app) {
            if (! $app->client_id || ! StaffClientVisibility::canAccessAdminRecord((int) $app->client_id)) {
                continue;
            }
            $partner = $app->partner?->partner_name ?: 'Application';
            $results[] = [
                'record_type' => StaffFileSession::RECORD_TYPE_STUDENT,
                'record_id' => (int) $app->client_id,
                'application_id' => (int) $app->id,
                'label' => $partner.' · #'.$app->id,
            ];
        }

        $partners = Partner::query()
            ->where('partner_name', 'like', $like)
            ->orderBy('partner_name')
            ->limit(8)
            ->get(['id', 'partner_name']);

        foreach ($partners as $partner) {
            $results[] = [
                'record_type' => StaffFileSession::RECORD_TYPE_PARTNER,
                'record_id' => (int) $partner->id,
                'application_id' => null,
                'label' => (string) $partner->partner_name,
            ];
        }

        return response()->json([
            'success' => true,
            'results' => array_slice($results, 0, 20),
        ]);
    }

    protected function staffOrAbort(): Staff
    {
        $staff = Auth::guard('admin')->user();
        if (! $staff instanceof Staff) {
            abort(403, 'Unauthorized');
        }

        return $staff;
    }
}
