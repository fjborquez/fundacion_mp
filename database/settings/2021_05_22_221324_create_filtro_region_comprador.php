<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateFiltroRegionComprador extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('etl.filtro_region_comprador', 'región de los lagos;región de atacama');
    }
}
