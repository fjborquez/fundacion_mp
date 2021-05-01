<?php

namespace App\Services\MercadoPublico\Validadores;

use Illuminate\Support\Arr;

class ValidadorAdjudicacion {
    public function ejecutar($licitacion) {
        if (Arr::exists($licitacion, 'Adjudicacion') && $licitacion['Adjudicacion'] !== null) {
            return true;
        }

        return false;
    }
}