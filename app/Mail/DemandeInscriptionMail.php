<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class DemandeInscriptionMail extends Mailable
{
public $data;

public function __construct($data)
{
$this->data = $data;
}

public function build()
{
return $this->subject('Nouvelle demande inscription')
->view('emails.demande');
}
}
