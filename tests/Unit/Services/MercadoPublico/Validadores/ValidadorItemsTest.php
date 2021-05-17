<?php

namespace Tests\Unit;

use App\Services\MercadoPublico\Validadores\ValidadorItems;
use ErrorException;
use Tests\TestCase;

class ValidadorItemsTest extends TestCase
{
    protected $licitacionConAdjudicaciones;
    protected $licitacionSinAdjudicaciones;

    public function setUp(): void
    {
        parent::setUp();
        $this->setUpLicitacionConAdjudicaciones();
        $this->setUpLicitacionSinAdjudicaciones();
    }

    private function setUpLicitacionConAdjudicaciones()
    {
        $path = storage_path("testing/json/licitacion.json");
        $this->openLicitacion($path, $this->licitacionConAdjudicaciones);
    }

    private function setUpLicitacionSinAdjudicaciones() 
    {
        $path = storage_path("testing/json/licitacion_sin_adjudicaciones.json");
        $this->openLicitacion($path, $this->licitacionSinAdjudicaciones);
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
    public function test_Should_ReturnTrue_When_ItemsIsNotEmpty()
    {
        $modificador = new ValidadorItems();
        $modificadorReturn = $modificador->ejecutar($this->licitacionConAdjudicaciones);
        
        $this->assertTrue($modificadorReturn);
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_Should_ReturnFalse_When_ItemsIsEmpty()
    {
        $modificador = new ValidadorItems();
        $modificadorReturn = $modificador->ejecutar($this->licitacionSinAdjudicaciones);
        
        $this->assertFalse($modificadorReturn);
    }
}
