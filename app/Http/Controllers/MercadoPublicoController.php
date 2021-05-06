<?php

namespace App\Http\Controllers;

use App\Services\MercadoPublico\MercadoPublicoETL;
use Illuminate\Http\Request;

class MercadoPublicoController extends Controller
{
    public function index(Request $request, MercadoPublicoETL $mercadoPublicoETL) {
        return $mercadoPublicoETL->generarETL($request->input('sendToSalesforce'));
    }
}
