<?php

namespace App\Services\MercadoPublico\Modificadores;

use App\Services\MercadoPublico\Modificadores\Modificador;

class ModificadorTramoMonto extends Modificador {
    public function ejecutar(&$licitacion) {
        foreach($licitacion['Items']['Listado'] as $key => &$item) {
            $item['Adjudicacion']['TramoMonto'] = 'B1';

            if ($item['Adjudicacion']['MontoTotal'] > 10000000) {
                $item['Adjudicacion']['TramoMonto'] = 'A1';
            }
        }

        return true;
    }
}
