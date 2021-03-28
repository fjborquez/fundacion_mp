<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateFiltroPalabrasExcluidasSetting extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.mercado_publico_filtro_palabras_excluidas', 'correo electronicos;internet;telefonía movil;medicamentos;mg;ml;fármaco;convenio;examenes;servicios;remedios;materiales;ferreteria;guardias;seguridad;vigilancia;autos;vehículos;motos;camiones;buses;arriendos;adquisición;aseo;juguetes;caja alimentos;mercadería;combustible;desechable;guantes;protectores;mascarillas;laboratorios;pdi;fuerzas militares;carabineros;Fuerza aérea');
    }
}
