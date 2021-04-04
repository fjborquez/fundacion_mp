<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateSalesforceSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.mercado_publico_salesforce_record_type_id', '0121U000001O0IQQA0');
        $this->migrator->add('general.mercado_publico_salesforce_default_firstname', 'CONTACTO');
        $this->migrator->add('general.mercado_publico_salesforce_default_lastname', 'GENERICO');
        $this->migrator->add('general.mercado_publico_segundos_entre_consultas', '5');
    }
}
