<?php

namespace App\Services\MercadoPublico\Filtros;

class FiltroTipoLicitacion extends Filtro {
    public function filtrar($licitacion) {
        if (!in_array($licitacion['Tipo'], $this->listasPalabras['listaTipoLicitacionPermitidos'])) {
            return false;
        }

        return true;
    }
}