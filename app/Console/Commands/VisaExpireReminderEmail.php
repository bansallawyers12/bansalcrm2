<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Models\Admin;
use App\Models\CrmEmailTemplate;
use Carbon\Carbon;
//use Mail;
use Auth;


use App\Mail\CommonMail;

use Illuminate\Support\Facades\Mail;

class VisaExpireReminderEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'VisaExpireReminderEmail:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Visa Expire Reminder Email before 15 days';


    protected function send_compose_template($content, $sendername, $to = null, $subject = null, $sender = null, $array = array(), $cc = array())
	{
		$emailService = app(\App\Services\EmailService::class);
		$emailConfig = $emailService->configureMailerForEmail($sender);
		if (!$emailConfig) {
			return false;
		}
		$sender = $emailConfig->email;
		$sendername = $sendername ?: ($emailConfig->display_name ?? $emailConfig->email);

		$explodeTo = explode(';', $to);//for multiple and single to
		$q = Mail::mailer('ses')->to($explodeTo);
			if(!empty($cc)){
				$q->cc($cc);
			}
		$q->send(new CommonMail($content, $subject, $sender, $sendername, $array));
        // check for failures
		if ( Mail::flushMacros() ) { //Mail::failures()
            return false;
		}

		// otherwise everything is okay ...
		return true;

	}

    /**
     * Execute the console command.
     */
    public function handle()
    {  
        $query 	= \App\Models\Admin::select('id','visaexpiry','email','first_name','last_name')
        //->where('is_visa_expire_mail_sent', 2)
        ->whereNull('is_visa_expire_mail_sent')
        ->whereNotNull('visaexpiry')
        ->where('visaexpiry', '=', Carbon::now()->addDays(15)->toDateString() ) ;
        $totalLogs = $query->count(); //dd($totalLogs);
		$logs = $query->get(); //dd($logs);
        if($totalLogs >0){
            // Use first active From address from from_emails table (AWS SES)
            $defaultEmail = app(\App\Services\EmailService::class)->getDefaultEmail();
            $fromCompanyName = $defaultEmail ? $defaultEmail->display_name : 'Bansal Immigration';
			foreach($logs as $key=>$val){ //dd($val->id);

                $to_email = $val->email;
                $first_name = $val->first_name;
                $fullname = $val->first_name.' '.$val->last_name;
                $visaExpiry = date('d-m-Y',strtotime($val->visaExpiry));
                /*$details = [
                    'title' => 'Your visa is expiring soon',
                    'body' => 'This is for testing email using smtp',
                    'fullname' => $fullname,
                    'visaExpiry' => $visaExpiry
                ];
                $mail_sent = \Mail::to($to_email)->send(new \App\Mail\VisaExpireReminderMail($details));*/

                //visa expiry email reminder
                $crm_template_data 	= \App\Models\CrmEmailTemplate::select('*')->where('id', 35)->first();
                //dd($crm_template_data);
                if(!empty( $crm_template_data))
                {
                    $subject = $crm_template_data['subject'];
                    $subject = str_replace('{Client First Name}', $first_name, $subject);

                    $message = $crm_template_data['description'];
                    $message = str_replace('{Client First Name}',$first_name, $message);
                    $message = str_replace('{Visa Valid Upto}',$visaExpiry, $message);
                    $message = str_replace('{Company Name}',$fromCompanyName, $message);

                    $ccarray = array();
                    $array = array();

                    // Pass null for sender - send_compose_template uses first active email from DB
                    $mail_sent = $this->send_compose_template($message, $fromCompanyName, $to_email, $subject, null, $array,@$ccarray);
                    if($mail_sent){
                        $this->info('Mail is sent.');
                        $rec = \App\Models\Admin::find($val->id);
                        $rec->is_visa_expire_mail_sent = 1;
                        $rec->save();
                    } else {
                        $this->info('Mail not sent.');
                    }
                }
            }

        } else {
            $this->info('No record is found.');
        }
    }
}
