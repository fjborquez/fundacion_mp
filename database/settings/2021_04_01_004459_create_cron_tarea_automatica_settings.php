<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateCronTareaAutomaticaSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.mercado_publico_cron_tarea_automatica', '0 0 * * *');
    }
}
