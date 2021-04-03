<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\GeneralSettings;
use App\Services\Etl\MercadoPublicoETL;

class MercadoPublicoController extends Controller
{
    public function index(MercadoPublicoETL $mercadoPublicoETL) {
        return $mercadoPublicoETL->generarETL();
    }
}
