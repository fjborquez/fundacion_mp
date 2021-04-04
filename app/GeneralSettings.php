<?php

namespace App;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings {
    public string $mercado_publico_ticket;
    public string $mercado_publico_url_licitaciones;
    public string $mercado_publico_filtro_tipo_licitacion;
    public string $mercado_publico_filtro_palabras_excluidas;
    
    public string $mercado_publico_filtro_palabras_clave_educacion;
    public string $mercado_publico_filtro_palabras_clave_industria_creativa;
    public string $mercado_publico_filtro_palabras_clave_turismo;
    public string $mercado_publico_filtro_palabras_excluidas_educacion_y_cultura;
    public string $mercado_publico_filtro_palabras_clave_espacio_publico;
    public string $mercado_publico_filtro_palabras_clave_diseño;
    public string $mercado_publico_filtro_palabras_clave_vialidad;
    public string $mercado_publico_filtro_palabras_clave_obras_publicas;
    public string $mercado_publico_filtro_palabras_clave_salud;
    public string $mercado_publico_filtro_palabras_clave_inclusion;
    public string $mercado_publico_filtro_palabras_excluidas_desarrollo_social;
    public string $mercado_publico_filtro_palabras_clave_agua;
    public string $mercado_publico_filtro_palabras_clave_apr;
    public string $mercado_publico_filtro_palabras_clave_sistemas_alimentarios;
    public string $mercado_publico_filtro_palabras_clave_produccion_sostenible;
    public string $mercado_publico_filtro_palabras_clave_eficiencia_energetica;
    public string $mercado_publico_filtro_palabras_excluidas_medio_ambiente;

    public string $mercado_publico_cron_tarea_automatica;
    public string $mercado_publico_salesforce_record_type_id;
    public string $mercado_publico_salesforce_default_firstname;
    public string $mercado_publico_salesforce_default_lastname;
    public string $mercado_publico_segundos_entre_consultas;

    public static function group(): string {
        return 'general';
    }
} 