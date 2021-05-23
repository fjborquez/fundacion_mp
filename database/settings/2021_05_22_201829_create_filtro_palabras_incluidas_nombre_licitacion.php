<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateFiltroPalabrasIncluidasNombreLicitacion extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('etl.filtro_palabras_incluidas', 'servicio;mantenimiento');
    }
}
