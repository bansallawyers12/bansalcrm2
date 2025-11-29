<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\CheckinLog;
use Carbon\Carbon;

class InPersonCompleteTaskRemoval extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'InPersonCompleteTaskRemoval:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove InPerson complete tasks after 30 day';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //\Log::info('Every minute cron job testing!');
        $query 	= CheckinLog::select('id')
            ->where('status', '1')
            ->where('updated_at', '<=', Carbon::now()->subDays(30)->toDateTimeString() ) ;
        $totalLogs = $query->count();//dd($totalLogs);
		$logs = $query->get(); //dd($logs);
        if($totalLogs >0){
			foreach($logs as $key=>$val){ //dd($val->id);
                $CheckinLog = CheckinLog::find($val->id);
                $CheckinLog->delete();
            }
            $this->info('Record is deleted.');
        } else {
            $this->info('No record is found.');
        }
    }
}
