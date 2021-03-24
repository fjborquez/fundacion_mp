<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use BenTools\ETL\Etl;
use BenTools\ETL\EtlBuilder;
use BenTools\ETL\Transformer\CallableTransformer;
use App\GeneralSettings;
use Illuminate\Support\Facades\Log;

class MercadoPublicoController extends Controller
{
    public function index(GeneralSettings $settings) {
        $licitacionesProcesadas = [];
        // TOOD: Si viene de tarea automatica, no enviar a salesforce

        $ticket = $settings->mercado_publico_ticket;
        $url = $settings->mercado_publico_url_licitaciones;
        $fecha = Carbon::now()->format('dmY');

        $response = Http::get($url, [
            'fecha' => $fecha,
            'ticket' => $ticket
        ]);

        if ($response->successful()) {
            if ($response->collect()->has('Listado')) {
                $licitaciones = $response->collect()->get('Listado');
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
            } else {
                Log::error('Error al intentar rescatar listado de licitaciones. No hay campo Listado en el body: ');
                Log::error($response->body());
            }
        } else {
            Log::error('Error al intentar obtener datos de ' . $url . '?fecha=' . $fecha . '&ticket=' . $ticket);
        }
        
        return $licitacionesProcesadas;
    }
}
