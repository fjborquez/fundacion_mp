<?php

namespace App\Mail;

use App\Services\MercadoPublico\Settings\MailSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificacionMail extends Mailable
{
    use Queueable, SerializesModels;

    private $mailSettings;
    private $licitaciones;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($licitaciones)
    {
        $this->mailSettings = app(MailSettings::class);
        $this->licitaciones = $licitaciones;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->mailSettings->asunto, $this->licitaciones)
                    ->view('mail.notificacionMail')
                    ->with([
                        'asunto' => $this->mailSettings->asunto,
                        'licitaciones' => $this->licitaciones,
                    ]);
    }
}
