<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateModificadorAreaSectorClimatizacionSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('etl.filtro_palabras_clave_climatizacion', 'climatización;calefacción;pellet;caldera;estufa;geotermia;aire acondicionado;termica;termico');
    }
}
