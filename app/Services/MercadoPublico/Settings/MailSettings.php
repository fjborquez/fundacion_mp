<?php

namespace App\Services\MercadoPublico\Settings;

use Spatie\LaravelSettings\Settings;

class MailSettings extends Settings {
    public string $nombre_destinatario;
    public string $mail_destinatario;
    public string $asunto;

    public static function group(): string {
        return 'mail';
    }
}
