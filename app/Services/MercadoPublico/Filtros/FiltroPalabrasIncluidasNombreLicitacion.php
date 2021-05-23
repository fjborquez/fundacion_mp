<?php

namespace App\Services\MercadoPublico\Filtros;

use App\Services\MercadoPublico\Filtros\Filtro;
use Illuminate\Support\Str;

class FiltroPalabrasIncluidasNombreLicitacion extends Filtro {
    public function ejecutar($licitacion) {
        if (Str::of($licitacion['Nombre'])->contains($this->listasPalabras['listaPalabrasIncluidas'])) {
            return true;
        }

        return false;
    }
}
