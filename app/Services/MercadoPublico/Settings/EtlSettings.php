<?php

namespace App\Services\MercadoPublico\Settings;

use Spatie\LaravelSettings\Settings;

class EtlSettings extends Settings {
    public string $filtro_tipo_licitacion;
    public string $filtro_region_comprador;
    public string $filtro_palabras_incluidas;
    public string $filtro_palabras_excluidas;
    public string $filtro_palabras_clave_educacion;
    public string $filtro_palabras_clave_industria_creativa;
    public string $filtro_palabras_clave_turismo;
    public string $filtro_palabras_excluidas_educacion_y_cultura;
    public string $filtro_palabras_clave_espacio_publico;
    public string $filtro_palabras_clave_diseño;
    public string $filtro_palabras_clave_vialidad;
    public string $filtro_palabras_clave_obras_publicas;
    public string $filtro_palabras_clave_salud;
    public string $filtro_palabras_clave_inclusion;
    public string $filtro_palabras_excluidas_desarrollo_social;
    public string $filtro_palabras_clave_agua;
    public string $filtro_palabras_clave_apr;
    public string $filtro_palabras_clave_sistemas_alimentarios;
    public string $filtro_palabras_clave_produccion_sostenible;
    public string $filtro_palabras_clave_eficiencia_energetica;
    public string $filtro_palabras_clave_climatizacion;
    public string $filtro_palabras_excluidas_medio_ambiente;

    public string $cron_tarea_automatica;

    public static function group(): string {
        return 'etl';
    }
}
