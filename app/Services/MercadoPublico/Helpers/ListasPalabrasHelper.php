<?php

namespace App\Services\MercadoPublico\Helpers;

use App\Services\MercadoPublico\Settings\EtlSettings;

class ListasPalabrasHelper {
    private $etlSettings;

    public function __construct() {
        $this->etlSettings = app(EtlSettings::class);
    }

    public function generarListasPalabras() {
        $listaTipoLicitacionPermitidos = explode(';', $this->etlSettings->filtro_tipo_licitacion);
        $listaRegionComprador = explode(';', $this->etlSettings->filtro_region_comprador);
        $listaPalabrasExcluidas = explode(';', $this->etlSettings->filtro_palabras_excluidas);
        $listaPalabrasIncluidas = explode(';', $this->etlSettings->filtro_palabras_incluidas);
        $listaPalabrasClaveEducacion = explode(';', $this->etlSettings->filtro_palabras_clave_educacion);
        $listaPalabrasClaveIndustriaCreativa = explode(';', $this->etlSettings->filtro_palabras_clave_industria_creativa);
        $listaPalabrasClaveTurismo = explode(';', $this->etlSettings->filtro_palabras_clave_turismo);
        $listaPalabrasClaveEspacioPublico = explode(';', $this->etlSettings->filtro_palabras_clave_espacio_publico);
        $listaPalabrasClaveDiseño = explode(';', $this->etlSettings->filtro_palabras_clave_diseño);
        $listaPalabrasClaveVialidad = explode(';', $this->etlSettings->filtro_palabras_clave_vialidad);
        $listaPalabrasClaveObrasPublicas = explode(';', $this->etlSettings->filtro_palabras_clave_obras_publicas);
        $listaPalabrasClaveSalud = explode(';', $this->etlSettings->filtro_palabras_clave_salud);
        $listaPalabrasClaveInclusion = explode(';', $this->etlSettings->filtro_palabras_clave_inclusion);
        $listaPalabrasClaveAgua = explode(';', $this->etlSettings->filtro_palabras_clave_agua);
        $listaPalabrasClaveApr = explode(';', $this->etlSettings->filtro_palabras_clave_apr);
        $listaPalabrasClaveSistemasAlimentarios = explode(';', $this->etlSettings->filtro_palabras_clave_sistemas_alimentarios);
        $listaPalabrasClaveProduccionSostenible = explode(';', $this->etlSettings->filtro_palabras_clave_produccion_sostenible);
        $listaPalabrasClaveEficienciaEnergetica = explode(';', $this->etlSettings->filtro_palabras_clave_eficiencia_energetica);
        $listaPalabrasExcluidasEducacionYCultura = explode(';', $this->etlSettings->filtro_palabras_excluidas_educacion_y_cultura);
        $listaPalabrasExcluidasDesarrolloSocial = explode(';', $this->etlSettings->filtro_palabras_excluidas_desarrollo_social);
        $listaPalabrasExcluidasMedioAmbiente = explode(';', $this->etlSettings->filtro_palabras_excluidas_medio_ambiente);

        return [
            'listaTipoLicitacionPermitidos' => $listaTipoLicitacionPermitidos,
            'listaRegionComprador' => $listaRegionComprador,
            'listaPalabrasExcluidas' => $listaPalabrasExcluidas,
            'listaPalabrasIncluidas' => $listaPalabrasIncluidas,
            'educacion' => $listaPalabrasClaveEducacion,
            'industriaCreativa' => $listaPalabrasClaveIndustriaCreativa,
            'turismo' => $listaPalabrasClaveTurismo,
            'espacioPublico' => $listaPalabrasClaveEspacioPublico,
            'diseño' => $listaPalabrasClaveDiseño,
            'vialidad' => $listaPalabrasClaveVialidad,
            'obrasPublicas' => $listaPalabrasClaveObrasPublicas,
            'salud' => $listaPalabrasClaveSalud,
            'inclusion' => $listaPalabrasClaveInclusion,
            'agua' => $listaPalabrasClaveAgua,
            'apr' => $listaPalabrasClaveApr,
            'sistemaAlimentarios' => $listaPalabrasClaveSistemasAlimentarios,
            'produccionSostenible' => $listaPalabrasClaveProduccionSostenible,
            'eficienciaEnergetica' => $listaPalabrasClaveEficienciaEnergetica,
            'excluidasEducacionYCultura' => $listaPalabrasExcluidasEducacionYCultura,
            'excluidasDesarrolloSocial' => $listaPalabrasExcluidasDesarrolloSocial,
            'excluidasMedioAmbiente' => $listaPalabrasExcluidasMedioAmbiente,
        ];
    }
}
