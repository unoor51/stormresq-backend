<?php

namespace App\Mail;

use App\Models\Rescuer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApprovedRescuerAccount extends Mailable
{
    use Queueable, SerializesModels;
    public $rescuer;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Rescuer $rescuer)
    {
        $this->rescuer = $rescuer;
    }
    public function build()
    {
        $loginlink = url('/rescuer/login/');

        return $this->subject('Admin Approved Account')
                    ->view('emails.approved-rescuer')
                    ->with([
                        'rescuer' => $this->rescuer,
                        'loginlink' => $loginlink,
                    ]);
    }
}
