<?php

namespace App\Services\MercadoPublico\Filtros;

use App\Services\MercadoPublico\Filtros\Filtro;
use Illuminate\Support\Str;

class FiltroPalabraExcluidasNombreLicitacion extends Filtro {
    public function ejecutar($licitacion) {
        if (Str::of($licitacion['Nombre'])->contains($this->listasPalabras['listaPalabrasExcluidas'])) {
            return false;
        }

        return true;
    }
}