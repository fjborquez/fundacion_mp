<?php

namespace App\Services\MercadoPublico\Modificadores;

use App\Services\MercadoPublico\Modificadores\Modificador;

class ModificadorQuitarAdjudicacionesNulas extends Modificador {
    public function ejecutar(&$licitacion) {
        $adjudicaciones = $licitacion['Items']['Listado'];

        foreach($licitacion['Items']['Listado'] as $key => $item) {
            if (! is_array($item['Adjudicacion'])) {
                unset($adjudicaciones[$key]);
                $licitacion['Items']['Cantidad']--;
            }
        }

        $licitacion['Items']['Listado'] = $adjudicaciones;
        
        return true;
    }
}
