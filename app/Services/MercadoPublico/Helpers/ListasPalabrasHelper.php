<?php

namespace App\Services\MercadoPublico\Helpers;

use App\Services\MercadoPublico\Settings\EtlSettings;
use CodeInc\StripAccents\StripAccents;

class ListasPalabrasHelper {
    private $etlSettings;

    public function __construct() {
        $this->etlSettings = app(EtlSettings::class);
    }

    public function generarListasPalabras() {
        $listaTipoLicitacionPermitidos = explode(';', StripAccents::strip($this->etlSettings->filtro_tipo_licitacion));
        $listaRegionComprador = explode(';', StripAccents::strip($this->etlSettings->filtro_region_comprador));
        $listaPalabrasExcluidas = explode(';', StripAccents::strip($this->etlSettings->filtro_palabras_excluidas));
        $listaPalabrasIncluidas = explode(';', StripAccents::strip($this->etlSettings->filtro_palabras_incluidas));
        $listaPalabrasClaveEducacion = explode(';', StripAccents::strip($this->etlSettings->filtro_palabras_clave_educacion));
        $listaPalabrasClaveIndustriaCreativa = explode(';', StripAccents::strip($this->etlSettings->filtro_palabras_clave_industria_creativa));
        $listaPalabrasClaveTurismo = explode(';', StripAccents::strip($this->etlSettings->filtro_palabras_clave_turismo));
        $listaPalabrasClaveEspacioPublico = explode(';', StripAccents::strip($this->etlSettings->filtro_palabras_clave_espacio_publico));
        $listaPalabrasClaveDiseño = explode(';', StripAccents::strip($this->etlSettings->filtro_palabras_clave_diseño));
        $listaPalabrasClaveVialidad = explode(';', StripAccents::strip($this->etlSettings->filtro_palabras_clave_vialidad));
        $listaPalabrasClaveObrasPublicas = explode(';', StripAccents::strip($this->etlSettings->filtro_palabras_clave_obras_publicas));
        $listaPalabrasClaveSalud = explode(';', StripAccents::strip($this->etlSettings->filtro_palabras_clave_salud));
        $listaPalabrasClaveInclusion = explode(';', StripAccents::strip($this->etlSettings->filtro_palabras_clave_inclusion));
        $listaPalabrasClaveAgua = explode(';', StripAccents::strip($this->etlSettings->filtro_palabras_clave_agua));
        $listaPalabrasClaveApr = explode(';', StripAccents::strip($this->etlSettings->filtro_palabras_clave_apr));
        $listaPalabrasClaveSistemasAlimentarios = explode(';', StripAccents::strip($this->etlSettings->filtro_palabras_clave_sistemas_alimentarios));
        $listaPalabrasClaveProduccionSostenible = explode(';', StripAccents::strip($this->etlSettings->filtro_palabras_clave_produccion_sostenible));
        $listaPalabrasClaveEficienciaEnergetica = explode(';', StripAccents::strip($this->etlSettings->filtro_palabras_clave_eficiencia_energetica));
        $listaPalabrasExcluidasEducacionYCultura = explode(';', StripAccents::strip($this->etlSettings->filtro_palabras_excluidas_educacion_y_cultura));
        $listaPalabrasExcluidasDesarrolloSocial = explode(';', StripAccents::strip($this->etlSettings->filtro_palabras_excluidas_desarrollo_social));
        $listaPalabrasExcluidasMedioAmbiente = explode(';', StripAccents::strip($this->etlSettings->filtro_palabras_excluidas_medio_ambiente));

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
