<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Note;
use Carbon\Carbon;
use DB;

class MonthlyPartnerRecurringNotes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'MonthlyPartnerRecurringNotes:monthly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch partner notes with recurring deadlines on the 1st of each month.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $lastMonth = Carbon::now()->subMonth(); //dd( $lastMonth->year);

        $notes = Note::where('type', 'like', 'partner')
            ->where('deadline_recurring_type', 'like', 'yes')
            ->where('status', 0)
            ->whereMonth('note_deadline', $lastMonth->month)
            ->whereYear('note_deadline', $lastMonth->year)
            ->orderBy('id', 'desc')
            ->get(); //dd($notes);

        if(!empty($notes) && count($notes) >0){
            foreach($notes as $key=>$note){ //dd($note->id);
                // Update the status of the current note
                $noteInfo = Note::find($note->id);
                $noteInfo->status = 1;
                $noteInfo->updated_at = now(); // Set updated_at to current timestamp
                $noteInfo->save();

                // Insert a new note with the same details but with the next month's note_deadline
                $nextMonthDeadline = Carbon::parse($note->note_deadline)->addMonth();

                $newNote = $note->replicate(); // Copy the note
                $newNote->note_deadline = $nextMonthDeadline; // Update deadline
                $newNote->status = 0; // Reset status for the new note
                $newNote->updated_at = now(); // Set updated_at to current timestamp
                $newNote->save();
            }
            $this->info('Monthly partner notes query executed successfully.');
        } else {
            $this->info('No record is found.');
        }

        // Process the notes as needed
        /*foreach ($notes as $note) {
            // Add your processing logic here
            $this->info("Processing Note ID: {$note->id}");
        }

        $this->info('Monthly partner notes query executed successfully.');*/
    }
}
