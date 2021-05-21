<?php

namespace Tests\Unit;

use App\Services\MercadoPublico\Modificadores\ModificadorMontoTotal;
use ErrorException;
use Tests\TestCase;

class ModificadorMontoTotalTest extends TestCase
{
    protected $licitacionConAdjudicacionesNulas;
    protected $licitacionSinAdjudicacionesNulas;

    public function setUp(): void
    {
        parent::setUp();
        $this->setUpLicitacionConAdjudicacionesNulas();
        $this->setUpLicitacionSinAdjudicacionesNulas();
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
    public function test_Should_ThrowErrorException_When_AdjudicacionIsNull()
    {
        $this->expectException(ErrorException::class);

        $modificador = new ModificadorMontoTotal();
        $modificador->ejecutar($this->licitacionConAdjudicacionesNulas);
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_Should_ReturnTrueAndAddAdjudicacionMontoTotal_When_AdjudicacionIsNotNull()
    {
        $modificador = new ModificadorMontoTotal();
        $modificadorReturn = $modificador->ejecutar($this->licitacionSinAdjudicacionesNulas);
        
        $this->assertTrue($modificadorReturn);

        foreach($this->licitacionSinAdjudicacionesNulas['Items']['Listado'] as $item)
        {
            $adjudicacion = $item['Adjudicacion'];
            $montoTotal = $adjudicacion['Cantidad'] * $adjudicacion['MontoUnitario'];
            
            $this->assertArrayHasKey('MontoTotal', $adjudicacion);
            $this->assertEquals($montoTotal, $adjudicacion['MontoTotal']);
        }
    }
}
