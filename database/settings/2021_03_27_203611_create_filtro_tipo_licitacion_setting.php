<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateFiltroTipoLicitacionSetting extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.mercado_publico_filtro_tipo_licitacion', 'lp;lr;lq;ls;h2;i2;b2;o1;o2');
    }
}
