<?php

namespace App\Mail;

use App\Models\Rescuer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyRescuerEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $rescuer;
    public $frontendUrl;
    public function __construct(Rescuer $rescuer, $frontendUrl)
    {
        $this->rescuer = $rescuer;
        $this->frontendUrl = $frontendUrl;
    }

    public function build()
    {
        return $this->subject('Verify Your Email Address')
                    ->view('emails.verify-rescuer')
                    ->with([
                        'rescuer' => $this->rescuer,
                        'verificationUrl' => $this->frontendUrl,
                    ]);
    }
}