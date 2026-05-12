<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FollowupCalendarSetting;
use App\Models\FollowupConsultant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Admin UI for per-consultant free-slot calendar windows (stored in followup_calendar_settings).
 */
class FollowupCalendarSettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(): View
    {
        $this->syncFreeSettingsForActiveConsultants();

        $settings = FollowupCalendarSetting::query()
            ->where('service_type', 'free')
            ->with('consultant')
            ->get()
            ->sortBy(fn ($s) => $s->consultant?->sort_order ?? 999)
            ->values();

        return view('Admin.followups.calendar-settings.index', compact('settings'));
    }

    public function edit(FollowupCalendarSetting $followupCalendarSetting): View
    {
        $this->authorizeFreeSetting($followupCalendarSetting);
        $followupCalendarSetting->load('consultant');

        return view('Admin.followups.calendar-settings.edit', [
            'setting' => $followupCalendarSetting,
        ]);
    }

    public function update(Request $request, FollowupCalendarSetting $followupCalendarSetting): RedirectResponse
    {
        $this->authorizeFreeSetting($followupCalendarSetting);

        $validated = $request->validate([
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'slot_duration_minutes' => 'required|integer|in:5,10,15,20,30,45,60',
            'days' => 'nullable|array',
            'days.*' => 'integer|between:1,7',
            'is_active' => 'sometimes|boolean',
            'notes' => 'nullable|string|max:2000',
        ]);

        $start = $validated['start_time'];
        $end = $validated['end_time'];
        if (strtotime($end) <= strtotime($start)) {
            return redirect()->back()->withInput()->withErrors([
                'end_time' => 'End time must be after start time.',
            ]);
        }

        $days = isset($validated['days']) ? array_values(array_unique(array_map('intval', $validated['days']))) : [];

        $followupCalendarSetting->start_time = $start.':00';
        $followupCalendarSetting->end_time = $end.':00';
        $followupCalendarSetting->slot_duration_minutes = (int) $validated['slot_duration_minutes'];
        $followupCalendarSetting->available_days = $days;
        $followupCalendarSetting->is_active = $request->boolean('is_active');
        $followupCalendarSetting->notes = $validated['notes'] ?? null;
        $followupCalendarSetting->save();

        return redirect()
            ->to(followups_console_route('calendar-settings.index'))
            ->with('success', 'Calendar setting saved.');
    }

    protected function authorizeFreeSetting(FollowupCalendarSetting $setting): void
    {
        if ($setting->service_type !== 'free') {
            abort(404);
        }
    }

    protected function syncFreeSettingsForActiveConsultants(): void
    {
        $consultants = FollowupConsultant::query()
            ->where('status', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        foreach ($consultants as $consultant) {
            FollowupCalendarSetting::firstOrCreate(
                [
                    'followup_consultant_id' => $consultant->id,
                    'service_type' => 'free',
                ],
                [
                    'start_time' => '10:00:00',
                    'end_time' => '17:00:00',
                    'slot_duration_minutes' => 15,
                    'available_days' => [1, 2, 3, 4, 5],
                    'is_active' => true,
                    'notes' => $consultant->name.' — Free consultation',
                ]
            );
        }
    }
}
