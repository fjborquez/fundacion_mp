<?php

namespace Tests\Unit;

use App\Services\MercadoPublico\Filtros\FiltroNombreLicitacionExcluidosCategoria;
use ErrorException;
use Tests\TestCase;

class FiltroNombreLicitacionExcluidosCategoriaTest extends TestCase
{
    protected $licitacionConNombreExcluido;
    protected $licitacionSinNombreExcluido;

    public function setUp(): void
    {
        parent::setUp();
        $this->setUpLicitacionConNombreExcluido();
        $this->setUpLicitacionSinNombreExcluido();
    }

    private function setUpLicitacionConNombreExcluido()
    {
        $path = storage_path("testing/json/licitacion_nombre_area_sector_excluido.json");
        $this->openLicitacion($path, $this->licitacionConNombreExcluido);
    }

    private function setUpLicitacionSinNombreExcluido()
    {
        $path = storage_path("testing/json/licitacion_nombre_area_sector_sin_excluir.json");
        $this->openLicitacion($path, $this->licitacionSinNombreExcluido);
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
    public function test_Should_ReturnTrue_When_NombreLicitacionIsNotExcludedAndLower()
    {
        $modificador = new FiltroNombreLicitacionExcluidosCategoria();
        $this->licitacionSinNombreExcluido['Nombre'] = strtolower($this->licitacionSinNombreExcluido['Nombre']);
        $modificadorReturn = $modificador->ejecutar($this->licitacionSinNombreExcluido);
        
        $this->assertEquals($this->licitacionSinNombreExcluido['Nombre'], strtolower($this->licitacionSinNombreExcluido['Nombre']));
        $this->assertTrue($modificadorReturn);
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_Should_ReturnFalse_When_NombreLicitacionIsExcludedAndLower()
    {
        $modificador = new FiltroNombreLicitacionExcluidosCategoria();
        $this->licitacionConNombreExcluido['Nombre'] = strtolower($this->licitacionConNombreExcluido['Nombre']);
        $modificadorReturn = $modificador->ejecutar($this->licitacionConNombreExcluido);

        $this->assertEquals($this->licitacionConNombreExcluido['Nombre'], strtolower($this->licitacionConNombreExcluido['Nombre']));
        $this->assertFalse($modificadorReturn);
    }

    public function test_Should_ReturnTrue_When_NombreLicitacionIsExcludedAndUpper()
    {
        $modificador = new FiltroNombreLicitacionExcluidosCategoria();
        $this->licitacionSinNombreExcluido['Nombre'] = strtoupper($this->licitacionSinNombreExcluido['Nombre']);
        $modificadorReturn = $modificador->ejecutar($this->licitacionSinNombreExcluido);

        $this->assertEquals($this->licitacionSinNombreExcluido['Nombre'], strtoupper($this->licitacionSinNombreExcluido['Nombre']));
        $this->assertTrue($modificadorReturn);
    }
}
