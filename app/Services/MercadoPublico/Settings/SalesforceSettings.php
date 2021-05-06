<?php

namespace App\Services\MercadoPublico\Settings;

use Spatie\LaravelSettings\Settings;

class SalesforceSettings extends Settings {
    public string $record_type_id;
    public string $default_firstname;
    public string $default_lastname;
    public string $default_biographical_event_name;

    public static function group(): string {
        return 'salesforce';
    }
}
