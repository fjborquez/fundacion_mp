<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateRetryAndNameSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.mercado_publico_retry', '3');
        $this->migrator->add('general.mercado_publico_salesforce_default_biographical_event_name', 'Búsqueda Mercado Publico');

    }
}
