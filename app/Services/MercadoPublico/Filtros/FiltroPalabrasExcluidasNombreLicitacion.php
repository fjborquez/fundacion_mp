<?php

namespace App\Services\MercadoPublico\Filtros;

use App\Services\MercadoPublico\Filtros\Filtro;
use Illuminate\Support\Str;

class FiltroPalabrasExcluidasNombreLicitacion extends Filtro {
    public function ejecutar($licitacion) {
        if (Str::of($licitacion['Nombre'])->contains($this->listasPalabras['listaPalabrasExcluidas']) || 
            Str::of($licitacion['Descripcion'])->contains($this->listasPalabras['listaPalabrasExcluidas'])) {
            return false;
        }

        return true;
    }
}
