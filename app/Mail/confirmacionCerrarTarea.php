<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class confirmacionCerrarTarea extends Mailable
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
        return $this->subject('OT ing SVA '.$this->data['issueId'].' '.$this->data['action'])
				->view('confirmacionTareaCerradaMail')->with([
                        'name' => $this->data['name'],
            			'issueId' => $this->data['issueId'],
            			'token' => $this->data['token'],
                        'action' => $this->data['action'],
                        'msg' => $this->data['message'],
                    ]);
    }
}
