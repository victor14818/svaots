<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class verificacion extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $data;
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('OTs Ingeniería SVA - validación tarea '.$this->data['issueSubject'])
				->view('verificacionMail')->with([
                        'name' => $this->data['name'],
                        'email' => $this->data['email'],
                        'subject' => $this->data['issueSubject'],
                        'description' => $this->data['issueDescription'],
						'token' => $this->data['token'],
						'issueId' => $this->data['issueId'],
                    ]);
    }
}
