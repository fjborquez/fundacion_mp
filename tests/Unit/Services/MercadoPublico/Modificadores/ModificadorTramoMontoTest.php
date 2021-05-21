<?php

namespace Tests\Unit;

use App\Services\MercadoPublico\Modificadores\ModificadorTramoMonto;
use ErrorException;
use Tests\TestCase;

class ModificadorTramoMontoTest extends TestCase
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
        $path = storage_path("testing/json/licitacion_sin_adjudicaciones_nulas_y_monto_total.json");
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

        $modificador = new ModificadorTramoMonto();
        $modificador->ejecutar($this->licitacionConAdjudicacionesNulas);
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_Should_ReturnTrueAndTramoMontoEqualsA1_When_AdjudicacionIsNotNullAndMontoTotalGreaterThan100000000()
    {
        $modificador = new ModificadorTramoMonto();
        $modificadorReturn = $modificador->ejecutar($this->licitacionSinAdjudicacionesNulas);
        
        $this->assertTrue($modificadorReturn);

        foreach($this->licitacionSinAdjudicacionesNulas['Items']['Listado'] as $item)
        {
            $adjudicacion = $item['Adjudicacion'];
            
            if ($adjudicacion['MontoTotal'] > 100000000) {
                $this->assertArrayHasKey('TramoMonto', $adjudicacion);
                $this->assertEquals('A1', $adjudicacion['TramoMonto']);
            }
        }
    }

    public function test_Should_ReturnTrueAndTramoMontoEqualsB1_When_AdjudicacionIsNotNullAndMontoTotalLessThanOrEquals100000000()
    {
        $modificador = new ModificadorTramoMonto();
        $modificadorReturn = $modificador->ejecutar($this->licitacionSinAdjudicacionesNulas);
        
        $this->assertTrue($modificadorReturn);

        foreach($this->licitacionSinAdjudicacionesNulas['Items']['Listado'] as $item)
        {
            $adjudicacion = $item['Adjudicacion'];
            
            if ($adjudicacion['MontoTotal'] <= 100000000) {
                $this->assertArrayHasKey('TramoMonto', $adjudicacion);
                $this->assertEquals('B1', $adjudicacion['TramoMonto']);
            }
        }
    }
}
