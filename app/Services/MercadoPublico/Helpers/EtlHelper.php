<?php

namespace App\Services\MercadoPublico\Helpers;

use App\Services\MercadoPublico\Filtros\FiltroNoAplicado;
use App\Services\MercadoPublico\Modificadores\ModificadorNoAplicado;
use App\Services\MercadoPublico\Validadores\ValidadorNoAplicado;

class EtlHelper {
    public function aplicarValidadores($licitacion, $validadores, $etl) {
        $this->ejecutar($validadores, $licitacion, $etl,
            new ValidadorNoAplicado('La licitación no cumple con el formato valido para la ETL'));
    }

    public function aplicarFiltros($licitacion, $filtros, $etl) {
        $this->ejecutar($filtros, $licitacion, $etl,
            new FiltroNoAplicado('Licitacion no supera filtro'));
    }

    public function aplicarModificadores(&$licitacion, $modificadores, $etl) {
        $this->ejecutar($modificadores, $licitacion, $etl,
            new ModificadorNoAplicado('Licitacion no cumple con criterio de modificador'));
    }

    private function ejecutar($ejecutables, &$licitacion, $etl, $exception) {
        foreach($ejecutables as $ejecutable) {
            if (!$ejecutable->ejecutar($licitacion)) {
                $etl->skipCurrentItem();
                throw $exception;
            }
        }
    }
}
