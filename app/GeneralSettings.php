<?php

namespace App;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings {
    public string $mercado_publico_ticket;
    public string $mercado_publico_url_licitaciones;
    public string $mercado_publico_filtro_tipo_licitacion;
    public string $mercado_publico_filtro_palabras_excluidas;

    public static function group(): string {
        return 'general';
    }
} 