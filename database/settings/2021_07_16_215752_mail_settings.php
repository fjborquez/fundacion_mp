<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class MailSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('mail.nombre_destinatario', '');
        $this->migrator->add('mail.mail_destinatario', '');
        $this->migrator->add('mail.asunto', 'Notificación de Licitaciones Encontradas');
        $this->migrator->add('mail.texto', 'A continuación, un resumen de las licitaciones encontradas:');
    }
}
