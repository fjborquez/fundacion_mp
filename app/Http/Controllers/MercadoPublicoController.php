<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use BenTools\ETL\Etl;
use BenTools\ETL\EtlBuilder;
use BenTools\ETL\Transformer\CallableTransformer;

class MercadoPublicoController extends Controller
{
    public function index() {
        // TOOD: Si viene de tarea automatica, no enviar a salesforce

        // TODO: Obtener desde configuracion
        $ticket = "F8537A18-6766-4DEF-9E59-426B4FEE2844";

        // TODO: Obtener desde configuracion 
        $url = "http://api.mercadopublico.cl/servicios/v1/publico/licitaciones.json";

        // TOOD: Formato fecha desde configuracion
        $fecha = Carbon::now()->format('dmY');

        // TODO: Controlar codigo y mensaje de respuesta en caso de error
        // TODO: Contorlar que la respuesta tenga licitaciones y no sea un mensaje de error
        $response = Http::get($url, [
            'fecha' => $fecha,
            'ticket' => $ticket
        ]);
        
        $licitaciones = $response->collect()->get('Listado');
        $licitacionesProcesadas = [];
        $etl = EtlBuilder::init()
            ->transformWith(function($item) {
                yield array_map('strtolower', $item);
            })
            ->loadInto(
                function ($generated, $key, Etl $etl) use (&$licitacionesProcesadas) {
                    foreach ($generated as $licitacion) {
                        // TODO: Filtrar segun palabras clave/excluidas
                        if (str_contains($licitacion['Nombre'], 'suero')) {
                            $etl->skipCurrentItem();
                            break;
                        }
                        $licitacionesProcesadas[] = $licitacion;
                    }
                })
            ->createEtl();

        $etl->process($licitaciones);

        return $licitacionesProcesadas;
    }
}
