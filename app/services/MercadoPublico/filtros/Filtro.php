<?php

namespace App\Services\MercadoPublico\Filtros;

use App\Services\MercadoPublico\Settings\EtlSettings;
use App\Services\MercadoPublico\Helpers\ListasPalabrasHelper;

abstract class Filtro {
    private $etlSettings;
    private $listaPalabras;
    private $listasPalabrasHelper;

    public function __construct() {
        $this->etlSettings = app(EtlSettings::class);
        $this->listasPalabrasHelper = new ListasPalabrasHelper();
        $this->listasPalabras = $this->listasPalabrasHelper->generarListasPalabras();
    }

    abstract public function filtrar($licitacion);
}