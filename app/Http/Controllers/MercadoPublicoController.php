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
        $fecha = Carbon::now()->format('dmY');

        $listaTipoLicitacionPermitidos = explode(';', $settings->mercado_publico_filtro_tipo_licitacion);
        $listaPalabrasExcluidas = explode(';', $settings->mercado_publico_filtro_palabras_excluidas);
        $listaPalabrasClaveEducacion = explode(';', $settings->mercado_publico_filtro_palabras_clave_educacion);
        $listaPalabrasClaveIndustriaCreativa = explode(';', $settings->mercado_publico_filtro_palabras_clave_industria_creativa);
        $listaPalabrasClaveTurismo = explode(';', $settings->mercado_publico_filtro_palabras_clave_turismo);
        $listaPalabrasClaveEspacioPublico = explode(';', $settings->mercado_publico_filtro_palabras_clave_espacio_publico);
        $listaPalabrasClaveDiseño = explode(';', $settings->mercado_publico_filtro_palabras_clave_diseño);
        $listaPalabrasClaveVialidad = explode(';', $settings->mercado_publico_filtro_palabras_clave_vialidad);
        $listaPalabrasClaveObrasPublicas = explode(';', $settings->mercado_publico_filtro_palabras_clave_obras_publicas);
        $listaPalabrasClaveSalud = explode(';', $settings->mercado_publico_filtro_palabras_clave_salud);
        $listaPalabrasClaveInclusion = explode(';', $settings->mercado_publico_filtro_palabras_clave_inclusion);
        $listaPalabrasClaveAgua = explode(';', $settings->mercado_publico_filtro_palabras_clave_agua);
        $listaPalabrasClaveApr = explode(';', $settings->mercado_publico_filtro_palabras_clave_apr);
        $listaPalabrasClaveSistemasAlimentarios = explode(';', $settings->mercado_publico_filtro_palabras_clave_sistemas_alimentarios);
        $listaPalabrasClaveProduccionSostenible = explode(';', $settings->mercado_publico_filtro_palabras_clave_produccion_sostenible);
        $listaPalabrasClaveEficienciaEnergetica = explode(';', $settings->mercado_publico_filtro_palabras_clave_eficiencia_energetica);
        $listaPalabrasExcluidasEducacionYCultura = explode(';', $settings->mercado_publico_filtro_palabras_excluidas_educacion_y_cultura);
        $listaPalabrasExcluidasDesarrolloSocial = explode(';', $settings->mercado_publico_filtro_palabras_excluidas_desarrollo_social);
        $listaPalabrasExcluidasMedioAmbiente = explode(';', $settings->mercado_publico_filtro_palabras_excluidas_medio_ambiente);

        $listasPalabrasClavesCategorias = [
            'educacion' => $listaPalabrasClaveEducacion,
            'industriaCreativa' => $listaPalabrasClaveIndustriaCreativa,
            'turismo' => $listaPalabrasClaveTurismo,
            'espacioPublico' => $listaPalabrasClaveEspacioPublico,
            'diseño' => $listaPalabrasClaveDiseño,
            'vialidad' => $listaPalabrasClaveVialidad,
            'obrasPublicas' => $listaPalabrasClaveObrasPublicas,
            'salud' => $listaPalabrasClaveSalud,
            'inclusion' => $listaPalabrasClaveInclusion,
            'agua' => $listaPalabrasClaveAgua,
            'apr' => $listaPalabrasClaveApr,
            'sistemaAlimentarios' => $listaPalabrasClaveSistemasAlimentarios,
            'produccionSostenible' => $listaPalabrasClaveProduccionSostenible,
            'eficienciaEnergetica' => $listaPalabrasClaveEficienciaEnergetica,
            'excluidasEducacionYCultura' => $listaPalabrasExcluidasEducacionYCultura,
            'excluidasDesarrolloSocial' => $listaPalabrasExcluidasDesarrolloSocial,
            'excluidasMedioAmbiente' => $listaPalabrasExcluidasMedioAmbiente
        ];


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
                        function ($generated, $key, Etl $etl) use (&$licitacionesProcesadas, $listaTipoLicitacionPermitidos, $listaPalabrasExcluidas, $listasPalabrasClavesCategorias) {
                            foreach ($generated as $licitacion) {
                                if (!in_array($licitacion['Tipo'], $listaTipoLicitacionPermitidos)) {
                                    $etl->skipCurrentItem();
                                    break;
                                }

                                if (Str::of($licitacion['Nombre'])->contains($listaPalabrasExcluidas)) {
                                    $etl->skipCurrentItem();
                                    break;
                                }

                                if (Str::of($licitacion['Nombre'])->contains($listasPalabrasClavesCategorias['educacion'])) {
                                    $licitacion['area'] = 'educación y cultura';
                                    $licitacion['sector'] = 'educación';
                                } elseif (Str::of($licitacion['Nombre'])->contains($listasPalabrasClavesCategorias['industriaCreativa'])) {
                                    $licitacion['area'] = 'educación y cultura';
                                    $licitacion['sector'] = 'industria creativa';
                                } elseif (Str::of($licitacion['Nombre'])->contains($listasPalabrasClavesCategorias['turismo'])) {
                                    $licitacion['area'] = 'educación y cultura';
                                    $licitacion['sector'] = 'turismo';
                                } elseif (Str::of($licitacion['Nombre'])->contains($listasPalabrasClavesCategorias['espacioPublico'])) {
                                    $licitacion['area'] = 'desarrollo social';
                                    $licitacion['sector'] = 'espacio público';
                                } elseif (Str::of($licitacion['Nombre'])->contains($listasPalabrasClavesCategorias['diseño'])) {
                                    $licitacion['area'] = 'desarrollo social';
                                    $licitacion['sector'] = 'diseño';
                                } elseif (Str::of($licitacion['Nombre'])->contains($listasPalabrasClavesCategorias['vialidad'])) {
                                    $licitacion['area'] = 'desarrollo social';
                                    $licitacion['sector'] = 'vialidad';
                                } elseif (Str::of($licitacion['Nombre'])->contains($listasPalabrasClavesCategorias['obrasPublicas'])) {
                                    $licitacion['area'] = 'desarrollo social';
                                    $licitacion['sector'] = 'obras públicas';
                                } elseif (Str::of($licitacion['Nombre'])->contains($listasPalabrasClavesCategorias['salud'])) {
                                    $licitacion['area'] = 'desarrollo social';
                                    $licitacion['sector'] = 'salud';
                                } elseif (Str::of($licitacion['Nombre'])->contains($listasPalabrasClavesCategorias['inclusion'])) {
                                    $licitacion['area'] = 'desarrollo social';
                                    $licitacion['sector'] = 'inclusión';
                                } elseif (Str::of($licitacion['Nombre'])->contains($listasPalabrasClavesCategorias['agua'])) {
                                    $licitacion['area'] = 'medio ambiente';
                                    $licitacion['sector'] = 'agua';
                                } elseif (Str::of($licitacion['Nombre'])->contains($listasPalabrasClavesCategorias['apr'])) {
                                    $licitacion['area'] = 'medio ambiente';
                                    $licitacion['sector'] = 'apr';
                                } elseif (Str::of($licitacion['Nombre'])->contains($listasPalabrasClavesCategorias['sistemaAlimentarios'])) {
                                    $licitacion['area'] = 'medio ambiente';
                                    $licitacion['sector'] = 'sistemas alimentarios';
                                } elseif (Str::of($licitacion['Nombre'])->contains($listasPalabrasClavesCategorias['produccionSostenible'])) {
                                    $licitacion['area'] = 'medio ambiente';
                                    $licitacion['sector'] = 'producción sostenible';
                                } elseif (Str::of($licitacion['Nombre'])->contains($listasPalabrasClavesCategorias['eficienciaEnergetica'])) {
                                    $licitacion['area'] = 'medio ambiente';
                                    $licitacion['sector'] = 'eficiencia energética';
                                } else {
                                    $etl->skipCurrentItem();
                                    break;
                                }

                                if ($licitacion['area'] == 'educación y cultura' 
                                    && Str::of($licitacion['Nombre'])->contains($listasPalabrasClavesCategorias['excluidasEducacionYCultura'])) {
                                    $etl->skipCurrentItem();
                                    break;
                                }

                                if ($licitacion['area'] == 'desarrollo social' 
                                    && Str::of($licitacion['Nombre'])->contains($listasPalabrasClavesCategorias['excluidasDesarrolloSocial'])) {
                                    $etl->skipCurrentItem();
                                    break;
                                }

                                if ($licitacion['area'] == 'medio ambiente' 
                                    && Str::of($licitacion['Nombre'])->contains($listasPalabrasClavesCategorias['excluidasMedioAmbiente'])) {
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
