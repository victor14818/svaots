<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class aviso extends Mailable
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
        return $this->subject('OTs Ingeniería SVA - '. $this->data['project'] . ' - '. $this->data['issueSubject'] )
				->view('avisoMail')->with([
                        'subject' => $this->data['issueSubject'],
                        'phone' => $this->data['phone'],				
                        'project' => $this->data['project'],
                        'name' => $this->data['name'],
						'email' => $this->data['email'],
						'issueId' => $this->data['issueId'],
                        'description' => $this->data['issueDescription']
                    ]);
    }
}
