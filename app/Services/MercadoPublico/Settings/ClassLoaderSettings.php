<?php

namespace App\Services\MercadoPublico\Settings;

use Spatie\LaravelSettings\Settings;

class ClassLoaderSettings extends Settings {
    public string $premodificadores;
    public string $postmodificadores;
    public string $modificadores;
    public string $validadores;

    public static function group(): string {
        return 'classLoader';
    }
}
