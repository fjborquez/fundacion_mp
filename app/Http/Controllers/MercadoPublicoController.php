<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GeneralSettings;
use App\Services\MercadoPublico\MercadoPublicoETL;

class MercadoPublicoController extends Controller
{
    public function index(Request $request, MercadoPublicoETL $mercadoPublicoETL) {
        return $mercadoPublicoETL->generarETL($request->input('sendToSalesforce'));
    }
}
