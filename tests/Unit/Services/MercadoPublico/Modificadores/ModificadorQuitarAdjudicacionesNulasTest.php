<?php

namespace Tests\Unit;

use App\Services\MercadoPublico\Modificadores\ModificadorQuitarAdjudicacionesNulas;
use ErrorException;
use Tests\TestCase;

class ModificadorQuitarAdjudicacionesNulasTest extends TestCase
{
    protected $licitacionConAdjudicacionesNulas;

    public function setUp(): void
    {
        parent::setUp();
        $this->setUpLicitacionConAdjudicacionesNulas();
    }

    private function setUpLicitacionConAdjudicacionesNulas()
    {
        $path = storage_path("testing/json/licitacion.json");
        $this->openLicitacion($path, $this->licitacionConAdjudicacionesNulas);
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
    public function test_Should_ReturnTrueAndDeleteAdjudicacionAndCalculateTotalItems_When_AdjudicacionIsNull()
    {
        $modificador = new ModificadorQuitarAdjudicacionesNulas();
        $cantidad = $this->licitacionConAdjudicacionesNulas['Items']['Cantidad'];
        $modificadorReturn = $modificador->ejecutar($this->licitacionConAdjudicacionesNulas);
        
        $this->assertTrue($modificadorReturn);
        $this->assertLessThan($cantidad, $this->licitacionConAdjudicacionesNulas['Items']['Cantidad']);

        foreach($this->licitacionConAdjudicacionesNulas['Items']['Listado'] as $item)
        {
            $adjudicacion = $item['Adjudicacion'];
            $this->assertNotNull($adjudicacion);
        }
    }
}
