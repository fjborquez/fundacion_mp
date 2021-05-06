<?php

namespace App\Services\MercadoPublico\Filtros;

use App\Services\MercadoPublico\Helpers\ListasPalabrasHelper;
use App\Services\MercadoPublico\Settings\EtlSettings;

abstract class Filtro {
    protected $etlSettings;
    protected $listasPalabras;
    protected $listasPalabrasHelper;

    public function __construct() {
        $this->etlSettings = app(EtlSettings::class);
        $this->listasPalabrasHelper = new ListasPalabrasHelper();
        $this->listasPalabras = $this->listasPalabrasHelper->generarListasPalabras();
    }

    abstract public function ejecutar($licitacion);
}
