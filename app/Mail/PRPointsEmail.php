<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PRPointsEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $points;

    public function __construct($points)
    {
        $this->points = $points;
    }

    public function build()
    {
        return $this->subject('Australian PR Points Result')
                    ->view('emails.pr-points')
                    ->with(['points' => $this->points]);
    }
}
