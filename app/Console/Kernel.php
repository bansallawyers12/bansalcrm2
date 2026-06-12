<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        '\App\Console\Commands\CronJob',
       // '\App\Console\Commands\CompleteTaskRemoval',
        '\App\Console\Commands\InPersonCompleteTaskRemoval',
       // '\App\Console\Commands\VisaExpireReminderEmail',
       '\App\Console\Commands\MonthlyPartnerRecurringNotes',
        \App\Console\Commands\ExpireCrmAccessGrants::class,
        \App\Console\Commands\SesInboundSyncCommand::class,
        \App\Console\Commands\SesTestCommand::class,
        \App\Console\Commands\SendGridTestCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
		$schedule->command('CronJob:cronjob')->daily();
        //$schedule->command('CompleteTaskRemoval:daily')->daily();
        
        //InPerson Complete Task Removal daily 1 time
        $schedule->command('InPersonCompleteTaskRemoval:daily')->daily();
        //visa expire Reminder email before 15 days daily at 1 time
        //$schedule->command('VisaExpireReminderEmail:daily')->daily();

        //Fetch partner notes with recurring deadlines on the 1st of each month
       $schedule->command('MonthlyPartnerRecurringNotes:monthly')->monthly();

        $schedule->command('access:expire-grants')->hourly();

        // Poll SES inbound S3 bucket every minute and import new .eml files
        $schedule->command('ses:sync-inbound')->everyMinute()->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
       // $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
