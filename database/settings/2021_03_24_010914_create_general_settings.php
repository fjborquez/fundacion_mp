<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateGeneralSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.mercado_publico_ticket', 'F8537A18-6766-4DEF-9E59-426B4FEE2844');
        $this->migrator->add('general.mercado_publico_url_licitaciones', 'http://api.mercadopublico.cl/servicios/v1/publico/licitaciones.json');
    }
}
