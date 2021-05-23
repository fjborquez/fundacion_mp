<?php

namespace App\Services\MercadoPublico\Filtros;

use App\Services\MercadoPublico\Filtros\Filtro;

class FiltroRegionComprador extends Filtro {
    public function ejecutar($licitacion) {
        if (!in_array($licitacion['Comprador']['RegionUnidad'], $this->listasPalabras['listaRegionComprador'])) {
            return false;
        }

        return true;
    }
}
