<?php

namespace App\Services\MercadoPublico\Filtros;

use Illuminate\Support\Str;

class FiltroNombreLicitacionExcluidosCategoria extends Filtro {
    public function filtrar($licitacion) {
        $exclusiones = [
            'educación y cultura' => 'excluidasEducacionYCultura',
            'desarrollo social' => 'excluidasDesarrolloSocial',
            'medio ambiente' => 'excluidasMedioAmbiente'
        ];

        foreach ($exclusiones as $index => $excluidas) {
            if ($licitacion['area'] === $index 
                && Str::of($licitacion['Nombre'])->contains($this->listasPalabras[$excluidas])) {
                return false;
            }
        }

        return true;
    }
}