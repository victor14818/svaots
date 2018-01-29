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
        return $this->subject('OT '.$this->data['ot_proyecto'])
				->view('avisoMail')->with([
                        'asunto' => $this->data['asunto'],
                        'tel' => $this->data['tel'],				
                        'proyecto' => $this->data['ot_proyecto'],
                        'nombre' => $this->data['name'],
						'correo' => $this->data['mail'],
						'tarea_id' => $this->data['issue_id'],
                    ]);
    }
}
