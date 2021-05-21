<?php

namespace Tests\Unit;

use App\Services\MercadoPublico\Helpers\ListasPalabrasHelper;
use Tests\TestCase;

class ListasPalabrasHelperTest extends TestCase
{
    protected $listasPalabrasHelper;

    public function setUp(): void
    {
        parent::setUp();
        $this->listasPalabrasHelper = new ListasPalabrasHelper();
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_Should_ReturnMultidimensionalArray_When_ListaPalabrasIsGenerated()
    {
        $listaPalabras = $this->listasPalabrasHelper->generarListasPalabras();

        foreach($listaPalabras as $palabras) {
            $this->assertTrue(is_array($palabras));
        }
    }

    public function test_Should_ReturnLowerCase_When_ListaPalabrasIsGenerated()
    {
        $listaPalabras = $this->listasPalabrasHelper->generarListasPalabras();

        foreach($listaPalabras as $palabras) {
            foreach($palabras as $palabra) {
                $this->assertEquals(mb_strtolower($palabra, 'UTF-8'), $palabra);
            }
        }
    }
}
