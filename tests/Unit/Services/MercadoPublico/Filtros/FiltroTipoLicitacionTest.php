<?php

namespace Tests\Unit;

use App\Services\MercadoPublico\Filtros\FiltroTipoLicitacion;
use ErrorException;
use Tests\TestCase;

class FiltroTipoLicitacionTest extends TestCase
{
    protected $licitacionConTipoPermitido;
    protected $licitacionSinTipoPermitido;

    public function setUp(): void
    {
        parent::setUp();
        $this->setUpLicitacionConTipoPermitido();
        $this->setUpLicitacionSinTipoPermitido();
    }

    private function setUpLicitacionConTipoPermitido()
    {
        $path = storage_path("testing/json/licitacion.json");
        $this->openLicitacion($path, $this->licitacionConTipoPermitido);
    }

    private function setUpLicitacionSinTipoPermitido()
    {
        $path = storage_path("testing/json/licitacion_tipo_licitacion_no_permitido.json");
        $this->openLicitacion($path, $this->licitacionSinTipoPermitido);
    }

    private function openLicitacion($path, &$licitacion)
    {
        $json = file_get_contents($path);
        $licitacionJson = json_decode($json, true);
        $licitacion = $licitacionJson['Listado'][0];
    }

     /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_Should_ReturnTrue_When_TipoLicitacionIsAllowedAndLower()
    {
        $modificador = new FiltroTipoLicitacion();
        $this->licitacionConTipoPermitido['Tipo'] = strtolower($this->licitacionConTipoPermitido['Tipo']);
        $modificadorReturn = $modificador->ejecutar($this->licitacionConTipoPermitido);
        
        $this->assertTrue(ctype_lower($this->licitacionConTipoPermitido['Tipo']));
        $this->assertTrue($modificadorReturn);
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_Should_ReturnFalse_When_TipoLicitacionIsNotAllowedAndLower()
    {
        $modificador = new FiltroTipoLicitacion();
        $this->licitacionSinTipoPermitido['Tipo'] = strtolower($this->licitacionSinTipoPermitido['Tipo']);
        $modificadorReturn = $modificador->ejecutar($this->licitacionSinTipoPermitido);

        $this->assertTrue(ctype_lower($this->licitacionSinTipoPermitido['Tipo']));
        $this->assertFalse($modificadorReturn);
    }

    public function test_Should_ReturnFalse_When_TipoLicitacionIsUpper()
    {
        $modificador = new FiltroTipoLicitacion();
        $modificadorReturn = $modificador->ejecutar($this->licitacionConTipoPermitido);

        $this->assertTrue(ctype_upper($this->licitacionConTipoPermitido['Tipo']));
        $this->assertFalse($modificadorReturn);
    }
}
