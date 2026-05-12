<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Note;
use Illuminate\Support\Facades\Auth;

/**
 * Scheduled follow-ups (Notes with task_group = Followup).
 */
class FollowupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /** @var array<string, string> */
    public const CONSULTANT_LABELS = [
        'ankit' => 'Ankit Calendar',
        'rakshita' => 'Rakshita Calendar',
        'jaspreet' => 'Jaspreet Calendar',
        'syed' => 'Syed Calendar',
    ];

    public static function consultantLabel(string $slug): ?string
    {
        return self::CONSULTANT_LABELS[$slug] ?? null;
    }

    public static function followupNoteTitle(string $slug): ?string
    {
        $label = self::consultantLabel($slug);

        return $label ? 'Followup — '.$label : null;
    }

    protected function baseFollowupQuery()
    {
        $q = Note::query()
            ->where('type', 'client')
            ->where('is_action', 1)
            ->where('status', 0)
            ->where('task_group', 'Followup');

        if ((int) Auth::user()->role !== 1) {
            $q->where('assigned_to', Auth::user()->id);
        }

        return $q;
    }

    public function index()
    {
        $followups = $this->baseFollowupQuery()
            ->with([
                'noteClient:id,first_name,last_name,client_id,email',
                'assigned_user:id,first_name,last_name',
            ])
            ->whereNotNull('action_assign_date')
            ->orderByDesc('action_assign_date')
            ->paginate(40);

        return view('Admin.followups.index', compact('followups'));
    }

    public function calendar(string $consultant)
    {
        $title = self::followupNoteTitle($consultant);
        if ($title === null) {
            abort(404);
        }
        $consultantLabel = self::consultantLabel($consultant);

        $notes = $this->baseFollowupQuery()
            ->where('title', $title)
            ->whereNotNull('action_assign_date')
            ->with(['noteClient:id,first_name,last_name,client_id,email,phone'])
            ->orderBy('action_assign_date')
            ->get();

        $sched_res = [];
        foreach ($notes as $note) {
            $client = $note->noteClient;
            if (! $client) {
                continue;
            }
            $sched_res[$note->id] = [
                'id' => $note->id,
                'clientid' => $client->id,
                'stitle' => $client->client_id ?: ('#'.$client->id),
                'name' => base64_encode(trim($client->first_name.' '.$client->last_name)),
                'email' => base64_encode((string) ($client->email ?? '')),
                'phone' => base64_encode((string) ($client->phone ?? '')),
                'startdate' => date('Y-m-d', strtotime((string) $note->action_assign_date)),
                'end' => date('Y-m-d', strtotime((string) $note->action_assign_date)),
                'followup_date' => date('F j, Y g:i A', strtotime((string) $note->action_assign_date)),
                'description' => htmlspecialchars(strip_tags((string) $note->description), ENT_QUOTES, 'UTF-8'),
                'url' => url('/clients/detail/'.base64_encode(convert_uuencode($client->id))),
            ];
        }

        return view('Admin.followups.calendar', compact('sched_res', 'consultant', 'consultantLabel'));
    }
}
