<?php

namespace Tests\Unit;

use App\Services\MercadoPublico\Modificadores\ModificadorAreaSector;
use ErrorException;
use Tests\TestCase;

class ModificadorAreaSectorTest extends TestCase
{
    protected $licitacionConAdjudicacionesNulas;
    protected $licitacionSinAdjudicacionesNulas;
    protected $licitacionSinDescripcionExcluida;

    public function setUp(): void
    {
        parent::setUp();
        $this->setUpLicitacionConAdjudicacionesNulas();
        $this->setUpLicitacionSinAdjudicacionesNulas();
        $this->setUpLicitacionSinDescripcionExcluida();
    }

    private function setUpLicitacionConAdjudicacionesNulas()
    {
        $path = storage_path("testing/json/licitacion.json");
        $this->openLicitacion($path, $this->licitacionConAdjudicacionesNulas);
    }

    private function setUpLicitacionSinAdjudicacionesNulas() 
    {
        $path = storage_path("testing/json/licitacion_sin_adjudicaciones_nulas.json");
        $this->openLicitacion($path, $this->licitacionSinAdjudicacionesNulas);
    }

    private function setUpLicitacionSinDescripcionExcluida()
    {
        $path = storage_path("testing/json/licitacion_descripcion_no_excluida.json");
        $this->openLicitacion($path, $this->licitacionSinDescripcionExcluida);
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
    public function test_Should_ReturnTrueAndAddAreaAndSector_When_LicitacionHasNameToLower()
    {
        $modificador = new ModificadorAreaSector();
        $this->licitacionSinAdjudicacionesNulas['Nombre'] = strtolower($this->licitacionSinAdjudicacionesNulas['Nombre']);

        $modificadorReturn = $modificador->ejecutar($this->licitacionSinAdjudicacionesNulas);
        
        $this->assertTrue($modificadorReturn);
        $this->assertArrayHasKey('area', $this->licitacionSinAdjudicacionesNulas);
        $this->assertArrayHasKey('sector', $this->licitacionSinAdjudicacionesNulas);
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_Should_ReturnFalse_When_LicitacionHasNameToUpper()
    {
        $modificador = new ModificadorAreaSector();
        $this->licitacionSinAdjudicacionesNulas['Nombre'] = strtoupper($this->licitacionSinAdjudicacionesNulas['Nombre']);

        $modificadorReturn = $modificador->ejecutar($this->licitacionSinAdjudicacionesNulas);
        
        $this->assertFalse($modificadorReturn);
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_Should_ReturnFalse_When_LicitacionHasEmptyName()
    {
        $modificador = new ModificadorAreaSector();
        $this->licitacionSinAdjudicacionesNulas['Nombre'] = '';

        $modificadorReturn = $modificador->ejecutar($this->licitacionSinAdjudicacionesNulas);
        
        $this->assertFalse($modificadorReturn);
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_Should_ThrowErrorException_When_LicitacionNotHasName()
    {
        $this->expectException(ErrorException::class);

        $modificador = new ModificadorAreaSector();
        unset($this->licitacionSinAdjudicacionesNulas['Nombre']);

        $modificadorReturn = $modificador->ejecutar($this->licitacionSinAdjudicacionesNulas);
    }

    public function test_Should_ReturnTrueAndAddAreaAndSector_When_LicitacionHasDescriptionToLower()
    {
        $modificador = new ModificadorAreaSector();
        $this->licitacionSinDescripcionExcluida['Descripcion'] = strtolower($this->licitacionSinDescripcionExcluida['Descripcion']);

        $modificadorReturn = $modificador->ejecutar($this->licitacionSinDescripcionExcluida);
        
        $this->assertTrue($modificadorReturn);
        $this->assertArrayHasKey('area', $this->licitacionSinDescripcionExcluida);
        $this->assertArrayHasKey('sector', $this->licitacionSinDescripcionExcluida);
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_Should_ReturnFalse_When_LicitacionHasDescriptionToUpper()
    {
        $modificador = new ModificadorAreaSector();
        $this->licitacionSinDescripcionExcluida['Descripcion'] = strtoupper($this->licitacionSinDescripcionExcluida['Descripcion']);

        $modificadorReturn = $modificador->ejecutar($this->licitacionSinDescripcionExcluida);
        
        $this->assertFalse($modificadorReturn);
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_Should_ReturnFalse_When_LicitacionHasEmptyDescription()
    {
        $modificador = new ModificadorAreaSector();
        $this->licitacionSinDescripcionExcluida['Descripcion'] = '';

        $modificadorReturn = $modificador->ejecutar($this->licitacionSinDescripcionExcluida);
        
        $this->assertFalse($modificadorReturn);
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_Should_ThrowErrorException_When_LicitacionNotHasDescription()
    {
        $this->expectException(ErrorException::class);

        $modificador = new ModificadorAreaSector();
        unset($this->licitacionSinDescripcionExcluida['Descripcion']);

        $modificadorReturn = $modificador->ejecutar($this->licitacionSinDescripcionExcluida);
    }
}
