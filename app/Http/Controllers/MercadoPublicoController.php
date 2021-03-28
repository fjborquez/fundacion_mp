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
use Illuminate\Support\Str;

class MercadoPublicoController extends Controller
{
    public function index(GeneralSettings $settings) {
        $licitacionesProcesadas = [];
        // TOOD: Si viene de tarea automatica, no enviar a salesforce

        $ticket = $settings->mercado_publico_ticket;
        $url = $settings->mercado_publico_url_licitaciones;
        //$fecha = Carbon::now()->format('dmY');
        $fecha = '02022007';

        $listaTipoLicitacionPermitidos = explode(';', $settings->mercado_publico_filtro_tipo_licitacion);
        $listaPalabrasExcluidas = explode(';', $settings->mercado_publico_filtro_palabras_excluidas);

        $response = Http::retry(3, 1000)->get($url, [
            'fecha' => $fecha,
            'ticket' => $ticket
        ]);

        if ($response->successful()) {
            if ($response->collect()->has('Listado')) {
                $licitaciones = [];
                $licitacionesProcesadas = [];

                foreach($response->collect()->get('Listado') as $licitacionEnLista) {
                    sleep(5);

                    $resp = Http::retry(3, 500)->get($url, [
                        'ticket' => $ticket,
                        'codigo' => $licitacionEnLista['CodigoExterno']
                    ]);
    
                    if ($resp->successful()) {
                        if ($resp->collect()->has('Listado')) {
                            $licitaciones[] = $resp->collect()->get('Listado')[0];
                        } else {
                            Log::error('Error al intentar rescatar listado de licitaciones. No hay campo Listado en el body: ');
                            Log::error($resp->body());
                        }
                    } else {
                        Log::error('Error al intentar obtener datos de ' . $url . '?codigo=' . $licitacionEnLista['CodigoExterno'] . '&ticket=' . $ticket);
                    }
                }

                $etl = EtlBuilder::init()
                    ->transformWith(function($item) {
                        array_walk_recursive($item, function (&$value) {
                            $value = strtolower($value);
                        });

                        yield $item;
                    })
                    ->loadInto(
                        function ($generated, $key, Etl $etl) use (&$licitacionesProcesadas, $listaTipoLicitacionPermitidos, $listaPalabrasExcluidas) {
                            foreach ($generated as $licitacion) {
                                if (!in_array($licitacion['Tipo'], $listaTipoLicitacionPermitidos)) {
                                    $etl->skipCurrentItem();
                                    break;
                                }

                                if (Str::of($licitacion['Nombre'])->contains($listaPalabrasExcluidas)) {
                                    $etl->skipCurrentItem();
                                    break;
                                }

                                // TODO: Filtrar segun palabras clave/excluidas
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
