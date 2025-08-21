<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $model;       // Can be User or Rescuer
    public $frontendUrl;
    public $role;        // To differentiate between user & rescuer

    public function __construct($model, $frontendUrl, $role = 'user')
    {
        $this->model = $model;
        $this->frontendUrl = $frontendUrl;
        $this->role = $role;
    }

    public function build()
    {
        return $this->subject('Verify Your Email Address')
                    ->view('emails.verify')
                    ->with([
                        'model' => $this->model,
                        'verificationUrl' => $this->frontendUrl,
                        'role' => $this->role,
                    ]);
    }
}