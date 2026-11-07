<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetNotification extends Notification
{
    use Queueable;
    public $token;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        // Solo cambia la presentación. Mantener URL/token, remitente, asunto
        // y canal originales; el broker conserva vigencia y validación del token.
        // Los contactos usan la misma fuente de empresa que los correos del visor.
        $empresa = app(\App\Http\Controllers\EmpresaController::class)->get_empresa_data();
        return (new MailMessage)
            ->from(config('mail.from.address'), env('MAIL_SENDER'))
            ->subject(env('MAIL_FROM_NAME') . " | Recuperación de contraseña")
            ->view('mails.password_reset', [
                'resetUrl' => url('pages/reset-password', $this->token),
                'nombreDestinatario' => trim((string) ($notifiable->nombre ?? '')),
                'empresa' => $empresa,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
