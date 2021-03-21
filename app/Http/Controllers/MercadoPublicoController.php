<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class MercadoPublicoController extends Controller
{
    public function index() {
        // TODO: Obtener desde configuracion
        $ticket = "F8537A18-6766-4DEF-9E59-426B4FEE2844";

        // TODO: Obtener desde configuracion 
        $url = "http://api.mercadopublico.cl/servicios/v1/publico/licitaciones.json";

        // TOOD: Formato fecha desde configuracion
        $fecha = Carbon::now()->format('dmY');

        // TODO: Controlar codigo y mensaje de respuesta en caso de error
        $response = Http::get($url, [
            'fecha' => $fecha,
            'ticket' => $ticket
        ]);

        $licitaciones = $response->json();

        return $licitaciones;
    }
}
