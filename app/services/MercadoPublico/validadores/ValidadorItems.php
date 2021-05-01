<?php

namespace App\Services\MercadoPublico\Validadores;

use Illuminate\Support\Arr;

class ValidadorItems {
    public function ejecutar($licitacion) {
        if (Arr::exists($licitacion, 'Items') && !empty($licitacion['Items'])) {
            return true;
        }

        return false;
    }
}