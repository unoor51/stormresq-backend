<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RescuerResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $resetUrl;

    public function __construct($name, $resetUrl)
    {
        $this->name = $name;
        $this->resetUrl = $resetUrl;
    }

    public function build()
    {
        return $this->subject('Reset Your StormResQ Password')
                    ->view('emails.rescuer_reset')
                    ->with([
                        'name' => $this->name,
                        'resetUrl' => $this->resetUrl,
                    ]);
    }
}