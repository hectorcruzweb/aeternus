<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Http\Controllers\EmpresaController;
use Illuminate\Contracts\Queue\ShouldQueue;

class SeguimientoMail extends Mailable
{
    use Queueable, SerializesModels;
    public $seguimiento;
    public $empresa;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($seguimiento)
    {
        $get_funeraria       = new EmpresaController();
        $this->empresa             = $get_funeraria->get_empresa_data();
        $this->seguimiento = $seguimiento;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = "Control de Seguimientos";
        return $this->subject($subject)
            ->markdown('emails.seguimiento');
    }
}
