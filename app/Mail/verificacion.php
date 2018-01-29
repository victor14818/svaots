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
        return $this->subject('Validación tarea '.$this->data['issue_subject'])
				->view('verificacionMail')->with([
                        'name' => $this->data['name'],
                        'email' => $this->data['email'],
						'confirmation_token' => $this->data['confirmation_token'],
						'issue_id' => $this->data['issue_id'],
                    ]);
    }
}
