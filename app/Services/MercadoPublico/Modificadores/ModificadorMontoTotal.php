<?php

namespace App\Services\MercadoPublico\Modificadores;

use App\Services\MercadoPublico\Modificadores\Modificador;

class ModificadorMontoTotal extends Modificador {
    public function ejecutar(&$licitacion) {
        foreach($licitacion['Items']['Listado'] as $key => &$item) {
            $item['Adjudicacion']['MontoTotal'] = $item['Adjudicacion']['Cantidad'] * $item['Adjudicacion']['MontoUnitario'];
        }

        return true;
    }
}
