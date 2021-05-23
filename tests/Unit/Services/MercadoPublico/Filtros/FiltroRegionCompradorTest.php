<?php

namespace Tests\Unit;

use App\Services\MercadoPublico\Filtros\FiltroRegionComprador;
use ErrorException;
use Tests\TestCase;

class FiltroRegionCompradorTest extends TestCase
{
    protected $licitacionConRegionCompradorPermitida;
    protected $licitacionSinRegionCompradorPermitida;

    public function setUp(): void
    {
        parent::setUp();
        $this->setUpLicitacionConRegionCompradorPermitida();
        $this->setUpLicitacionSinRegionCompradorPermitida();
    }

    private function setUpLicitacionConRegionCompradorPermitida()
    {
        $path = storage_path("testing/json/licitacion.json");
        $this->openLicitacion($path, $this->licitacionConRegionCompradorPermitida);
    }

    private function setUpLicitacionSinRegionCompradorPermitida()
    {
        $path = storage_path("testing/json/licitacion_con_palabras_excluidas.json");
        $this->openLicitacion($path, $this->licitacionSinRegionCompradorPermitida);
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
    public function test_Should_ReturnTrue_When_RegionCompradorIsAllowedAndLower()
    {
        $modificador = new FiltroRegionComprador();
        $this->licitacionConRegionCompradorPermitida['Comprador']['RegionUnidad'] = trim(mb_strtolower($this->licitacionConRegionCompradorPermitida['Comprador']['RegionUnidad']));
        $modificadorReturn = $modificador->ejecutar($this->licitacionConRegionCompradorPermitida);
        
        $this->assertTrue($modificadorReturn);
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_Should_ReturnFalse_When_RegionCompradorIsNotAllowedAndLower()
    {
        $modificador = new FiltroRegionComprador();
        $this->licitacionSinRegionCompradorPermitida['Comprador']['RegionUnidad'] = trim(mb_strtolower($this->licitacionSinRegionCompradorPermitida['Comprador']['RegionUnidad']));
        $modificadorReturn = $modificador->ejecutar($this->licitacionSinRegionCompradorPermitida);

        $this->assertFalse($modificadorReturn);
    }

    public function test_Should_ReturnFalse_When_RegionCompradorIsUpper()
    {
        $modificador = new FiltroRegionComprador();
        $this->licitacionSinRegionCompradorPermitida['Comprador']['RegionUnidad'] = trim(mb_strtoupper($this->licitacionSinRegionCompradorPermitida['Comprador']['RegionUnidad']));
        $modificadorReturn = $modificador->ejecutar($this->licitacionSinRegionCompradorPermitida);

        $this->assertFalse($modificadorReturn);
    }
}
