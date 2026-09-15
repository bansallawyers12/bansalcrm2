<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Partner;
use App\Models\Staff;
use App\Models\StaffFileSession;
use App\Services\StaffFileSessionService;
use App\Support\StaffClientVisibility;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StaffFileSessionController extends Controller
{
    public function __construct(
        protected StaffFileSessionService $sessions,
    ) {
        $this->middleware('auth:admin');
    }

    public function heartbeat(Request $request): JsonResponse
    {
        $data = $this->validateHeartbeat($request);
        $staff = $this->staffOrAbort();
        $this->assertCanAccessRecord($data);

        $session = $this->sessions->heartbeat(
            (int) $staff->id,
            $data['record_type'],
            (int) $data['record_id'],
            $data['application_id'] ?? null,
            (int) $data['focused_seconds'],
        );

        return response()->json([
            'success' => true,
            'session' => $this->serializeSession($session),
        ]);
    }

    public function blur(Request $request): JsonResponse
    {
        $data = $this->validateHeartbeat($request);
        $staff = $this->staffOrAbort();
        $this->assertCanAccessRecord($data);

        $session = $this->sessions->blur(
            (int) $staff->id,
            $data['record_type'],
            (int) $data['record_id'],
            $data['application_id'] ?? null,
            (int) $data['focused_seconds'],
        );

        return response()->json([
            'success' => true,
            'session' => $this->serializeSession($session),
        ]);
    }

    public function idleCut(Request $request, StaffFileSession $session): JsonResponse
    {
        $staff = $this->staffOrAbort();
        $data = $request->validate([
            'idle_started_at' => ['required', 'date'],
        ]);

        $updated = $this->sessions->idleCut(
            (int) $staff->id,
            $session,
            Carbon::parse($data['idle_started_at']),
        );

        return response()->json([
            'success' => true,
            'session' => $this->serializeSession($updated),
        ]);
    }

    public function update(Request $request, StaffFileSession $session): JsonResponse
    {
        $staff = $this->staffOrAbort();
        $data = $request->validate([
            'confirmed_minutes' => ['required', 'integer', 'min:1', 'max:480'],
        ]);

        $updated = $this->sessions->updateMinutes(
            (int) $staff->id,
            $session,
            (int) $data['confirmed_minutes'],
        );

        return response()->json([
            'success' => true,
            'session' => $this->serializeSession($updated),
        ]);
    }

    public function destroy(StaffFileSession $session): JsonResponse
    {
        $staff = $this->staffOrAbort();
        $this->sessions->delete((int) $staff->id, $session);

        return response()->json(['success' => true]);
    }

    /**
     * @return array{
     *     record_type: string,
     *     record_id: int,
     *     application_id: ?int,
     *     focused_seconds: int
     * }
     */
    protected function validateHeartbeat(Request $request): array
    {
        $data = $request->validate([
            'record_type' => ['required', Rule::in([StaffFileSession::RECORD_TYPE_STUDENT, StaffFileSession::RECORD_TYPE_PARTNER])],
            'record_id' => ['required', 'integer', 'min:1'],
            'application_id' => ['nullable', 'integer', 'min:1'],
            'focused_seconds' => ['required', 'integer', 'min:0', 'max:86400'],
        ]);

        $appId = isset($data['application_id']) ? (int) $data['application_id'] : null;
        $data['application_id'] = $appId && $appId > 0 ? $appId : null;
        $data['record_id'] = (int) $data['record_id'];
        $data['focused_seconds'] = (int) $data['focused_seconds'];

        return $data;
    }

    /**
     * @param  array{record_type: string, record_id: int, application_id: ?int}  $data
     */
    protected function assertCanAccessRecord(array $data): void
    {
        if ($data['record_type'] === StaffFileSession::RECORD_TYPE_STUDENT) {
            if (! StaffClientVisibility::canAccessAdminRecord($data['record_id'])) {
                abort(403, 'Unauthorized');
            }

            if ($data['application_id'] !== null) {
                $belongs = Application::query()
                    ->where('id', $data['application_id'])
                    ->where('client_id', $data['record_id'])
                    ->exists();
                if (! $belongs) {
                    abort(422, 'Application does not belong to this student.');
                }
            }

            return;
        }

        if (! Partner::query()->where('id', $data['record_id'])->exists()) {
            abort(404, 'Partner not found.');
        }

        if ($data['application_id'] !== null) {
            abort(422, 'Partner sessions cannot include an application.');
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function serializeSession(StaffFileSession $session): array
    {
        return [
            'id' => $session->id,
            'status' => $session->status,
            'focused_seconds' => (int) $session->focused_seconds,
            'confirmed_minutes' => $session->confirmed_minutes,
            'is_reviewed_only' => (bool) $session->is_reviewed_only,
            'activities_log_id' => $session->activities_log_id,
        ];
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
