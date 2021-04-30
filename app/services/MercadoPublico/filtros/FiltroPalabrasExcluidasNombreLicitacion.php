<?php

namespace App\Services\MercadoPublico\Filtros;

use Illuminate\Support\Str;

class FiltroPalabraExcluidasNombreLicitacion extends Filtro {
    public function filtrar($licitacion) {
        if (Str::of($licitacion['Nombre'])->contains($this->listasPalabras['listaPalabrasExcluidas'])) {
            return false;
        }

        return true;
    }
}