<?php

namespace Tests\Unit;

use App\Services\MercadoPublico\Modificadores\ModificadorFormatoRutAdjudicacion;
use ErrorException;
use Tests\TestCase;

class ModificadorFormatoRutAdjudicacionTest extends TestCase
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

        $modificador = new ModificadorFormatoRutAdjudicacion();
        $modificador->ejecutar($this->licitacionConAdjudicacionesNulas);
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_Should_ReturnTrueAndAddRemoveDotsFromRutProveedor_When_AdjudicacionIsNotNull()
    {
        $modificador = new ModificadorFormatoRutAdjudicacion();
        $modificadorReturn = $modificador->ejecutar($this->licitacionSinAdjudicacionesNulas);
        
        $this->assertTrue($modificadorReturn);

        foreach($this->licitacionSinAdjudicacionesNulas['Items']['Listado'] as $item)
        {
            $adjudicacion = $item['Adjudicacion'];
            $rutProveedor = $adjudicacion['RutProveedor'];
            
            $this->assertFalse(str_contains($rutProveedor, '.'));
        }
    }
}
