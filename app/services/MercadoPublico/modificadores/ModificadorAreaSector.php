<?php

namespace App\Services\MercadoPublico\Modificadores;

use Illuminate\Support\Str;

class ModificadorAreaSector extends Modificador {
    public function ejecutar(&$licitacion) {
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
    
                return $licitacion;
            }
        }
        
        return false;
    }
}