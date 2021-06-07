<?php

namespace Tests\Unit;

use App\Services\MercadoPublico\Filtros\FiltroPalabrasExcluidasNombreLicitacion;
use ErrorException;
use Tests\TestCase;

class FiltroPalabrasExcluidasNombreLicitacionTest extends TestCase
{
    protected $licitacionConNombreExcluido;
    protected $licitacionSinNombreExcluido;
    protected $icitacionConDescripcionExcluida;
    protected $icitacionSinDescripcionExcluida;

    public function setUp(): void
    {
        parent::setUp();
        $this->setUpLicitacionConNombreExcluido();
        $this->setUpLicitacionSinNombreExcluido();
        $this->setUpLicitacionConDescripcionExcluida();
        $this->setUpLicitacionSinDescripcionExcluida();
    }

    private function setUpLicitacionConNombreExcluido()
    {
        $path = storage_path("testing/json/licitacion_con_palabras_excluidas.json");
        $this->openLicitacion($path, $this->licitacionConNombreExcluido);
    }

    private function setUpLicitacionSinNombreExcluido()
    {
        $path = storage_path("testing/json/licitacion.json");
        $this->openLicitacion($path, $this->licitacionSinNombreExcluido);
    }

    private function setUpLicitacionConDescripcionExcluida()
    {
        $path = storage_path("testing/json/licitacion_con_palabras_excluidas_descripcion.json");
        $this->openLicitacion($path, $this->licitacionConDescripcionExcluida);
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
    public function test_Should_ReturnTrue_When_NombreLicitacionIsNotExcludedAndLower()
    {
        $modificador = new FiltroPalabrasExcluidasNombreLicitacion();
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
        $modificador = new FiltroPalabrasExcluidasNombreLicitacion();
        $this->licitacionConNombreExcluido['Nombre'] = strtolower($this->licitacionConNombreExcluido['Nombre']);
        $modificadorReturn = $modificador->ejecutar($this->licitacionConNombreExcluido);

        $this->assertEquals($this->licitacionConNombreExcluido['Nombre'], strtolower($this->licitacionConNombreExcluido['Nombre']));
        $this->assertFalse($modificadorReturn);
    }

    public function test_Should_ReturnTrue_When_NombreLicitacionIsExcludedAndUpper()
    {
        $modificador = new FiltroPalabrasExcluidasNombreLicitacion();
        $this->licitacionSinNombreExcluido['Nombre'] = strtoupper($this->licitacionSinNombreExcluido['Nombre']);
        $modificadorReturn = $modificador->ejecutar($this->licitacionSinNombreExcluido);

        $this->assertEquals($this->licitacionSinNombreExcluido['Nombre'], strtoupper($this->licitacionSinNombreExcluido['Nombre']));
        $this->assertTrue($modificadorReturn);
    }

    public function test_Should_ReturnTrue_When_DescripcionLicitacionIsNotExcludedAndLower()
    {
        $modificador = new FiltroPalabrasExcluidasNombreLicitacion();
        $this->licitacionSinDescripcionExcluida['Descripcion'] = strtolower($this->licitacionSinDescripcionExcluida['Descripcion']);
        $modificadorReturn = $modificador->ejecutar($this->licitacionSinDescripcionExcluida);
        
        $this->assertEquals($this->licitacionSinDescripcionExcluida['Descripcion'], strtolower($this->licitacionSinDescripcionExcluida['Descripcion']));
        $this->assertTrue($modificadorReturn);
    }

    public function test_Should_ReturnFalse_When_DescripcionLicitacionIsExcludedAndLower()
    {
        $modificador = new FiltroPalabrasExcluidasNombreLicitacion();
        $this->licitacionConDescripcionExcluida['Descripcion'] = strtolower($this->licitacionConDescripcionExcluida['Descripcion']);
        $modificadorReturn = $modificador->ejecutar($this->licitacionConDescripcionExcluida);

        $this->assertEquals($this->licitacionConDescripcionExcluida['Descripcion'], strtolower($this->licitacionConDescripcionExcluida['Descripcion']));
        $this->assertFalse($modificadorReturn);
    }

    public function test_Should_ReturnTrue_When_DescripcionLicitacionIsExcludedAndUpper()
    {
        $modificador = new FiltroPalabrasExcluidasNombreLicitacion();
        $this->licitacionSinDescripcionExcluida['Descripcion'] = strtoupper($this->licitacionSinDescripcionExcluida['Descripcion']);
        $modificadorReturn = $modificador->ejecutar($this->licitacionSinDescripcionExcluida);

        $this->assertEquals($this->licitacionSinDescripcionExcluida['Descripcion'], strtoupper($this->licitacionSinDescripcionExcluida['Descripcion']));
        $this->assertTrue($modificadorReturn);
    }
}
