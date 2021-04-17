<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateSalesforceSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('salesforce.record_type_id', '0121U000001O0IQQA0');
        $this->migrator->add('salesforce.default_firstname', 'CONTACTO');
        $this->migrator->add('salesforce.default_lastname', 'GENERICO');
        $this->migrator->add('salesforce.default_biographical_event_name', 'Búsqueda Mercado Publico');
    }
}
