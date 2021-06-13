<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateClassLoaderSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('classLoader.premodificadores', 'FiltroTipoLicitacion;FiltroPalabrasExcluidasNombreLicitacion');
        $this->migrator->add('classLoader.postmodificadores', 'FiltroNombreLicitacionExcluidosCategoria');
        $this->migrator->add('classLoader.modificadores', 'ModificadorAreaSector;ModificadorQuitarAdjudicacionesNulas;ModificadorFormatoRutAdjudicacion;ModificadorMontoTotal;ModificadorTramoMonto');
        $this->migrator->add('classLoader.validadores', 'ValidadorAdjudicacion;ValidadorItems');
    }
}
