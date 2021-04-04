<?php

namespace App\Services\Etl;

use App\GeneralSettings;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use BenTools\ETL\Etl;
use BenTools\ETL\EtlBuilder;
use BenTools\ETL\Transformer\CallableTransformer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Omniphx\Forrest\Providers\Laravel\Facades\Forrest;

class MercadoPublicoETL {
    public function generarETL() {
        $licitacionesProcesadas = [];
        $configuraciones = $this->obtenerConfiguraciones();
        $licitaciones = $this->obtenerLicitaciones();

        $etl = EtlBuilder::init()
            ->transformWith(function($item) {
                array_walk_recursive($item, function (&$value) {
                    $value = mb_convert_encoding(Str::lower($value), "UTF-8");
                });
        
                yield $item;
            })
            ->loadInto(
                function ($generated, $key, Etl $etl) use (&$licitacionesProcesadas, $configuraciones) {
                    Forrest::authenticate();
                    $listasPalabras = $configuraciones['listasPalabras'];

                    foreach ($generated as $licitacion) {
                        if (!Arr::exists($licitacion, 'Adjudicacion') || $licitacion['Adjudicacion'] == null) {
                            $etl->skipCurrentItem();
                            break;
                        }

                        if (!Arr::exists($licitacion, 'Items') || count($licitacion['Items']) < 1) {
                            $etl->skipCurrentItem();
                            break;
                        }

                        if (!in_array($licitacion['Tipo'], $listasPalabras['listaTipoLicitacionPermitidos'])) {
                            $etl->skipCurrentItem();
                            break;
                        }

                        if (Str::of($licitacion['Nombre'])->contains($listasPalabras['listaPalabrasExcluidas'])) {
                            $etl->skipCurrentItem();
                            break;
                        }

                        if (Str::of($licitacion['Nombre'])->contains($listasPalabras['educacion'])) {
                            $licitacion['area'] = 'educación y cultura';
                            $licitacion['sector'] = 'educación';
                        } elseif (Str::of($licitacion['Nombre'])->contains($listasPalabras['industriaCreativa'])) {
                            $licitacion['area'] = 'educación y cultura';
                            $licitacion['sector'] = 'industria creativa';
                        } elseif (Str::of($licitacion['Nombre'])->contains($listasPalabras['turismo'])) {
                            $licitacion['area'] = 'educación y cultura';
                            $licitacion['sector'] = 'turismo';
                        } elseif (Str::of($licitacion['Nombre'])->contains($listasPalabras['espacioPublico'])) {
                            $licitacion['area'] = 'desarrollo social';
                            $licitacion['sector'] = 'espacio público';
                        } elseif (Str::of($licitacion['Nombre'])->contains($listasPalabras['diseño'])) {
                            $licitacion['area'] = 'desarrollo social';
                            $licitacion['sector'] = 'diseño';
                        } elseif (Str::of($licitacion['Nombre'])->contains($listasPalabras['vialidad'])) {
                            $licitacion['area'] = 'desarrollo social';
                            $licitacion['sector'] = 'vialidad';
                        } elseif (Str::of($licitacion['Nombre'])->contains($listasPalabras['obrasPublicas'])) {
                            $licitacion['area'] = 'desarrollo social';
                            $licitacion['sector'] = 'obras públicas';
                        } elseif (Str::of($licitacion['Nombre'])->contains($listasPalabras['salud'])) {
                            $licitacion['area'] = 'desarrollo social';
                            $licitacion['sector'] = 'salud';
                        } elseif (Str::of($licitacion['Nombre'])->contains($listasPalabras['inclusion'])) {
                            $licitacion['area'] = 'desarrollo social';
                            $licitacion['sector'] = 'inclusión';
                        } elseif (Str::of($licitacion['Nombre'])->contains($listasPalabras['agua'])) {
                            $licitacion['area'] = 'medio ambiente';
                            $licitacion['sector'] = 'agua';
                        } elseif (Str::of($licitacion['Nombre'])->contains($listasPalabras['apr'])) {
                            $licitacion['area'] = 'medio ambiente';
                            $licitacion['sector'] = 'apr';
                        } elseif (Str::of($licitacion['Nombre'])->contains($listasPalabras['sistemaAlimentarios'])) {
                            $licitacion['area'] = 'medio ambiente';
                            $licitacion['sector'] = 'sistemas alimentarios';
                        } elseif (Str::of($licitacion['Nombre'])->contains($listasPalabras['produccionSostenible'])) {
                            $licitacion['area'] = 'medio ambiente';
                            $licitacion['sector'] = 'producción sostenible';
                        } elseif (Str::of($licitacion['Nombre'])->contains($listasPalabras['eficienciaEnergetica'])) {
                            $licitacion['area'] = 'medio ambiente';
                            $licitacion['sector'] = 'eficiencia energética';
                        } else {
                            $etl->skipCurrentItem();
                            break;
                        }

                        if ($licitacion['area'] == 'educación y cultura' 
                            && Str::of($licitacion['Nombre'])->contains($listasPalabras['excluidasEducacionYCultura'])) {
                            $etl->skipCurrentItem();
                            break;
                        }

                        if ($licitacion['area'] == 'desarrollo social' 
                            && Str::of($licitacion['Nombre'])->contains($listasPalabras['excluidasDesarrolloSocial'])) {
                            $etl->skipCurrentItem();
                            break;
                        }

                        if ($licitacion['area'] == 'medio ambiente' 
                            && Str::of($licitacion['Nombre'])->contains($listasPalabras['excluidasMedioAmbiente'])) {
                            $etl->skipCurrentItem();
                            break;
                        }

                        foreach($licitacion['Items']['Listado'] as $item) {
                            if (Arr::exists($item, 'Adjudicacion') && is_array($item['Adjudicacion'])) {
                                $rutProveedor = str_replace('.', '', $item['Adjudicacion']['RutProveedor']);
                                $nombreProveedor = $item['Adjudicacion']['NombreProveedor'];
            
                                $accountResponse = Forrest::query('SELECT Id FROM Account WHERE DNI__c = \'' . $rutProveedor . '\'');
            
                                $accountId = '';
                                $leadId = '';
            
                                if ($accountResponse['totalSize'] > 0) {
                                    $accountId = $accountResponse['records'][0]['Id'];
                                    Forrest::sobjects('Account/' . $accountId,[
                                            'method' => 'patch',
                                            'body'   => [
                                                'DNI__c' => $rutProveedor,
                                                'Company' => $nombreProveedor,
                                                'Area__c' => $licitacion['area'],
                                                'Industry' => $licitacion['sector']
                                            ]
                                        ]);
                                } else {
                                    $leadResponse = Forrest::query('SELECT Id FROM LEAD WHERE DNI__c = \'' . $rutProveedor . '\'');
            
                                    if ($leadResponse['totalSize'] > 0) {
                                        $leadId = $leadResponse['records'][0]['Id'];
                                        Forrest::sobjects('Lead/' . $leadId,[
                                            'method' => 'patch',
                                            'body'   => [
                                                'DNI__c' => $rutProveedor,
                                                'Company' => $nombreProveedor,
                                                'Area__c' => $licitacion['area'],
                                                'Industry' => $licitacion['sector']
                                            ]
                                        ]);
                                    } else {
                                        // TODO: FirstName y LastName configurables
                                        $addLeadResponse = Forrest::sobjects('Lead',[
                                            'method' => 'post',
                                            'body'   => [
                                                'FirstName' => 'CONTACTO',
                                                'LastName' => 'GENERICO',
                                                'DNI__c' => $rutProveedor,
                                                'Company' => $nombreProveedor,
                                                'Address__c' => '',
                                                'Area__c' => $licitacion['area'],
                                                'Industry' => $licitacion['sector']
                                            ]
                                        ]);
            
                                        $leadId = $addLeadResponse['id'];
                                    }
                                }
                                
                                // TODO: RecordTypeId como configuracion
                                Forrest::sobjects('BiographicalEvent__c',[
                                    'method' => 'post',
                                    'body'   => [
                                        'BidId__c' => $licitacion['CodigoExterno'],
                                        'BidName__c' => $licitacion['Nombre'],
                                        'Description__c' => $licitacion['Descripcion'],
                                        'BidAmount__c' => $licitacion['MontoEstimado'],
                                        'BidType__c' => $licitacion['Tipo'],
                                        'BidOrganization__c' => $licitacion['Comprador']['NombreOrganismo'],
                                        'Lead__c' => $leadId,
                                        'Account__c' => $accountId,
                                        'RecordTypeId' => '0121U000001O0IQQA0'
                                    ]
                                ]);
                            } 
                        }

                        $licitacionesProcesadas[] = $licitacion;
                    }
                })
            ->createEtl();

        $etl->process($licitaciones);
        
        return $licitacionesProcesadas;
    }

    function obtenerLicitaciones() {
        $licitaciones = [];
        $configuraciones = $this->obtenerConfiguraciones();

        $response = Http::retry(3, 5000)->get($configuraciones['url'], [
            'fecha' => $configuraciones['fecha'],
            'ticket' => $configuraciones['ticket']
        ]);

        if ($response->successful() ) {
            if ($response->collect()->has('Listado')) {
                foreach($response->collect()->get('Listado') as $licitacionEnLista) {
                    sleep(5);

                    $resp = Http::retry(3, 5000)->get($configuraciones['url'], [
                        'ticket' => $configuraciones['ticket'],
                        'codigo' => $licitacionEnLista['CodigoExterno']
                    ]);
    
                    if ($resp->successful()) {
                        if ($resp->collect()->has('Listado')) {
                            $licitaciones[] = $resp->collect()->get('Listado')[0];
                        } else {
                            Log::error('Error al intentar consultar datos de licitacion desde ' . $configuraciones['url'] . '?codigo=' . $licitacionEnLista['CodigoExterno'] . '&ticket=' . $configuraciones['ticket']);
                            throw new Exception('Error al intentar consultar datos de licitacion');
                        }
                    } else {
                        Log::error('Error al intentar obtener datos de licitacion desde ' . $configuraciones['url'] . '?codigo=' . $licitacionEnLista['CodigoExterno'] . '&ticket=' . $configuraciones['ticket']);
                        throw new Exception('Error al intentar obtener datos de licitacion');
                    }
                }

                return $licitaciones;
            } else {
                Log::error('Error al intentar consultar licitaciones desde ' . $configuraciones['url'] . '?fecha=' . $configuraciones['fecha'] . '&ticket=' . $configuraciones['ticket']);
                throw new Exception('Error al intentar consultar licitaciones');
            }
        } else {
            Log::error('Error al intentar obtener listado  de licitaciones desde ' . $configuraciones['url'] . '?fecha=' . $configuraciones['fecha'] . '&ticket=' . $configuraciones['ticket']);
            throw new Exception('Error al intentar obtener listado de licitaciones');
        }
    }

    function obtenerConfiguraciones() {
        $settings = app(GeneralSettings::class);

        $configuraciones = [];
        $configuraciones['ticket'] = $settings->mercado_publico_ticket;
        $configuraciones['url'] = $settings->mercado_publico_url_licitaciones;
        $configuraciones['fecha'] = Carbon::now()->format('dmY');

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

        $configuraciones['listasPalabras'] = [
            'listaTipoLicitacionPermitidos' => $listaTipoLicitacionPermitidos,
            'listaPalabrasExcluidas' => $listaPalabrasExcluidas,
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

        return $configuraciones;
    }
}