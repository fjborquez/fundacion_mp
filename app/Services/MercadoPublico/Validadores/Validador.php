<?php

namespace App\Services\MercadoPublico\Validadores;

abstract class Validador {
    abstract public function ejecutar($licitacion);
}
