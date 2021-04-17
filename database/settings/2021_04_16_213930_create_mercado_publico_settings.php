<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateMercadoPublicoSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('mercadopublico.ticket', 'F8537A18-6766-4DEF-9E59-426B4FEE2844');
        $this->migrator->add('mercadopublico.url_licitaciones', 'http://api.mercadopublico.cl/servicios/v1/publico/licitaciones.json');
        $this->migrator->add('mercadopublico.retry', '3');
        $this->migrator->add('mercadopublico.segundos_entre_consultas', '5');
        $this->migrator->add('mercadopublico.milisegundos_entre_consultas', '5000');
    }
}
