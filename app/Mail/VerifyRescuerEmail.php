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

    public function __construct(Rescuer $rescuer)
    {
        $this->rescuer = $rescuer;
    }

    public function build()
    {
        $verificationUrl = url('/api/rescuer/verify/' . $this->rescuer->verification_token);

        return $this->subject('Verify Your Email Address')
                    ->view('emails.verify-rescuer')
                    ->with([
                        'rescuer' => $this->rescuer,
                        'verificationUrl' => $verificationUrl,
                    ]);
    }
}