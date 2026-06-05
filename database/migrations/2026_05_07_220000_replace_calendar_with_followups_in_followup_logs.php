<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Align stored follow-up titles and activity HTML with display labels (Calendar → Followups).
     */
    public function up(): void
    {
        $replacements = [
            'Ankit Calendar' => 'Ankit Followups',
            'Rakshita Calendar' => 'Rakshita Followups',
            'Jaspreet Calendar' => 'Jaspreet Followups',
            'Syed Calendar' => 'Syed Followups',
        ];

        foreach ($replacements as $from => $to) {
            if (Schema::hasTable('notes')) {
                $legacyTitle = 'Followup — '.$from;

                $notesTitle = DB::table('notes')->where('title', $legacyTitle);
                if (Schema::hasColumn('notes', 'task_group')) {
                    $notesTitle->where('task_group', 'Followup');
                }
                $notesTitle->update(['title' => 'Followup — '.$to]);

                if (Schema::hasColumn('notes', 'description')) {
                    $notesDesc = DB::table('notes')->where('description', 'like', '%'.$from.'%');
                    if (Schema::hasColumn('notes', 'task_group')) {
                        $notesDesc->where('task_group', 'Followup');
                    } elseif (Schema::hasColumn('notes', 'title')) {
                        $notesDesc->where('title', 'like', 'Followup —%');
                    }
                    foreach ($notesDesc->cursor() as $row) {
                        $newDesc = str_replace($from, $to, (string) $row->description);
                        if ($newDesc !== $row->description) {
                            DB::table('notes')->where('id', $row->id)->update(['description' => $newDesc]);
                        }
                    }
                }
            }

            if (! Schema::hasTable('activities_logs')) {
                continue;
            }

            $logs = DB::table('activities_logs')
                ->where(function ($q) use ($from) {
                    $q->where('subject', 'like', '%'.$from.'%')
                        ->orWhere('description', 'like', '%'.$from.'%');
                });

            if (Schema::hasColumn('activities_logs', 'task_group')) {
                $logs->where('task_group', 'Followup');
            } elseif (Schema::hasColumn('activities_logs', 'subject')) {
                $logs->where('subject', 'like', 'Scheduled follow-up (%');
            }

            foreach ($logs->cursor() as $row) {
                $newSubject = str_replace($from, $to, (string) $row->subject);
                $newDesc = str_replace($from, $to, (string) $row->description);
                if ($newSubject !== $row->subject || $newDesc !== $row->description) {
                    DB::table('activities_logs')->where('id', $row->id)->update([
                        'subject' => $newSubject,
                        'description' => $newDesc,
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        // Not reversed: would undo intentional labelling for unrelated rows containing the same substring.
    }
};
