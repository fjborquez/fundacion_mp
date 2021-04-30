<?php

namespace App\Services\MercadoPublico\Helpers;

use App\Services\MercadoPublico\Settings\EtlSettings;
use App\Services\MercadoPublico\Clients\BancaEticaSalesforceClient;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Exception;
use DomainException;

class EtlHelper {
    private $etlSettings;
    private $listasPalabras;
    private $listasPalabrasHelper;

    public function __construct() {
        $this->etlSettings = app(EtlSettings::class);
        $this->listasPalabrasHelper = new ListasPalabrasHelper();
        $this->listasPalabras = $this->listasPalabrasHelper->generarListasPalabras();
    }

    public function comprobarFormatoLicitacionValido($licitacion) {
        if (Arr::exists($licitacion, 'Adjudicacion') && $licitacion['Adjudicacion'] !== null) {
            return;
        }

        if (Arr::exists($licitacion, 'Items') && !empty($licitacion['Items'])) {
            return;
        }

        throw new DomainException('La licitación no cumple con el formato valido para la ETL.');
    }

    public function filtrarPorTipoLicitacion($licitacion) {
        if (!in_array($licitacion['Tipo'], $this->listasPalabras['listaTipoLicitacionPermitidos'])) {
            return false;
        }

        return true;
    }

    public function filtrarPorPalabrasExcluidasNombreLicitacion($licitacion) {
        if (Str::of($licitacion['Nombre'])->contains($this->listasPalabras['listaPalabrasExcluidas'])) {
            return false;
        }

        return true;
    }

    public function categorizarLicitacion(&$licitacion) {
        // TODO: Pasar textos de area/sector a constante u otro
        $categorias = [
            'educacion' => [
                'area' => 'educación y cultura',
                'sector' => 'educación'
            ],
            'industriaCreativa' => [
                'area' => 'educación y cultura',
                'sector' => 'industria creativa',
            ],
            'turismo' => [
                'area' => 'educación y cultura',
                'sector' => 'turismo'
            ],
            'espacioPublico' => [
                'area' => 'desarrollo social',
                'sector' => 'espacio público'
            ],
            'diseño' => [
                'area' => 'desarrollo social',
                'sector' => 'diseño'
            ],
            'vialidad' => [
                'area' => 'desarrollo social',
                'sector' => 'vialidad'
            ],
            'obrasPublicas' => [
                'area' => 'desarrollo social',
                'sector' => 'obras públicas'
            ],
            'salud' => [
                'area' => 'desarrollo social',
                'sector' => 'salud'
            ],
            'inclusion' => [
                'area' => 'desarrollo social',
                'sector' => 'inclusión'
            ],
            'agua' => [
                'area' => 'medio ambiente',
                'sector' => 'agua'
            ],
            'apr' => [
                'area' => 'medio ambiente',
                'sector' => 'apr'
            ],
            'sistemaAlimentarios' => [
                'area' => 'medio ambiente',
                'sector' => 'sistemas alimentarios'
            ],
            'produccionSostenible' => [
                'area' => 'medio ambiente',
                'sector' => 'producción sostenible'
            ],
            'eficienciaEnergetica' => [
                'area' => 'medio ambiente',
                'sector' => 'eficiencia energética'
            ]
        ];

        foreach ($categorias as $indice => $categoria) {
            if (Str::of($licitacion['Nombre'])->contains($this->listasPalabras[$indice])) {
                $licitacion['area'] = $categoria['area'];
                $licitacion['sector'] = $categoria['sector'];
    
                return true;
            }
        }
        
        return false;
    }

    public function filtrarPorNombreLicitacionExcluidosCategoria($licitacion) {
        // TODO: Pasar textos de area/sector a constante u otro
        $exclusiones = [
            'educación y cultura' => 'excluidasEducacionYCultura',
            'desarrollo social' => 'excluidasDesarrolloSocial',
            'medio ambiente' => 'excluidasMedioAmbiente'
        ];

        foreach ($exclusiones as $index => $excluidas) {
            if ($licitacion['area'] === $index 
                && Str::of($licitacion['Nombre'])->contains($this->listasPalabras[$excluidas])) {
                return true;
            }
        }

        return false;
    }
}
