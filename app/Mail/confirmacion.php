<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class confirmacion extends Mailable
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
        return $this->subject('OTs Ingeniería SVA - confirmación ingreso tarea ' . $this->data['issueId'] . ' - ' . $this->data['issueSubject'])
				->view('confirmacionMail')->with([
                        'name' => $this->data['name'],
                        'token' => $this->data['token'],
            			'issueId' => $this->data['issueId'],
            			'email' => $this->data['email'],
                        'subject' => $this->data['issueSubject'],
                        'description' => $this->data['issueDescription'],
                    ]);
    }
}
