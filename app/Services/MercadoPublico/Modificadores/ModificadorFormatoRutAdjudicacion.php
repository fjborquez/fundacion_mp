<?php

namespace App\Services\MercadoPublico\Modificadores;

use App\Services\MercadoPublico\Modificadores\Modificador;

class ModificadorFormatoRutAdjudicacion extends Modificador {
    public function ejecutar(&$licitacion) {
        foreach($licitacion['Items']['Listado'] as &$item) {
            $item['Adjudicacion']['RutProveedor'] = str_replace('.', '', $item['Adjudicacion']['RutProveedor']);
        }
        
        return true;
    }
}
