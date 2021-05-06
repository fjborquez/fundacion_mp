<?php

namespace App\Services\MercadoPublico\Settings;

use Spatie\LaravelSettings\Settings;

class MercadoPublicoSettings extends Settings {
    public string $ticket;
    public string $url_licitaciones;
    public string $retry;
    public string $segundos_entre_consultas;
    public string $milisegundos_entre_consultas;

    public static function group(): string {
        return 'mercadopublico';
    }
}
