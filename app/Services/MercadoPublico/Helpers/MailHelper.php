<?php

namespace App\Services\MercadoPublico\Helpers;

use App\Services\MercadoPublico\Settings\MailSettings;
use Illuminate\Support\Facades\Mail;

class MailHelper {
    private $mailSettings;

    public function __construct() {
        $this->mailSettings = app(MailSettings::class);
    }

    public function enviarLicitacionesAMail($licitacionesProcesadas, $sendToMail) {
        if ($sendToMail) {
            Mail::to($this->mailSettings->mail_destinatario)->send(new \App\Mail\NotificacionMail($licitacionesProcesadas));
        }
    }
}
