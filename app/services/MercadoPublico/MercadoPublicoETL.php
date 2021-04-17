<?php

namespace App\Services\MercadoPublico;

use App\Services\MercadoPublico\Clients\MercadoPublicoHttpClient;
use App\Services\MercadoPublico\Clients\BancaEticaSalesforceClient;
use App\Services\MercadoPublico\Settings\SalesforceSettings;
use App\Services\MercadoPublico\Settings\EtlSettings;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use BenTools\ETL\Etl;
use BenTools\ETL\EtlBuilder;
use BenTools\ETL\Transformer\CallableTransformer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Omniphx\Forrest\Providers\Laravel\Facades\Forrest;
use BenTools\ETL\EventDispatcher\Event\EndProcessEvent;
use BenTools\ETL\EventDispatcher\Event\ItemExceptionEvent;
use RuntimeException;
use Exception;

class MercadoPublicoETL {
    private $salesforceSettings;
    private $etlSettings;

    function __construct() {
        $this->salesforceSettings = app(SalesforceSettings::class);
        $this->etlSettings = app(EtlSettings::class);
    }

    public function generarETL($sendToSalesforce = false) {
        $licitaciones = [];
        // TODO: Externalizar bloqueo
        $fp = fopen(storage_path('framework/locks/etlmp.txt'), "r+");
        
        $this->bloquear($fp);
        $licitaciones = $this->ejecutar($sendToSalesforce);
        $this->desbloquear($fp);
        
        return $licitaciones;
    }

    public function ejecutar($sendToSalesforce = false) {
        Log::info('Ha iniciado el proceso de ETL');

        $licitacionesProcesadas = [];
        $configuraciones = $this->obtenerConfiguraciones($sendToSalesforce);
        $mercadoPublicoHttpClient = new MercadoPublicoHttpClient();
        $licitaciones = $mercadoPublicoHttpClient->obtenerLicitacionesConDetalles($configuraciones['fecha']);

        if ($configuraciones['sendToSalesforce']) {
            Log::info('Se enviaran licitaciones a Salesforce.');
        }

        // TODO: Refactorizar funciones de etl
        $etl = EtlBuilder::init()
            ->transformWith(function($item) {
                array_walk_recursive($item, function (&$value) {
                    $value = mb_convert_encoding(Str::lower($value), "UTF-8");
                });
        
                yield $item;
            })
            ->loadInto(
                function ($generated, $key, Etl $etl) use (&$licitacionesProcesadas, $configuraciones) {
                    $listasPalabras = $configuraciones['listasPalabras'];
                    $bancaEticaSalesforceClient = new BancaEticaSalesforceClient();

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

                        if ($configuraciones['sendToSalesforce']) {
                            foreach($licitacion['Items']['Listado'] as $item) {
                                if (Arr::exists($item, 'Adjudicacion') && is_array($item['Adjudicacion'])) {
                                    $rutProveedor = str_replace('.', '', $item['Adjudicacion']['RutProveedor']);
                                    $nombreProveedor = $item['Adjudicacion']['NombreProveedor'];
                
                                    $accountResponse = $bancaEticaSalesforceClient->obtenerAccountPorRut($rutProveedor);
                
                                    $account['id'] = '';
                                    $lead['id'] = '';
                
                                    if ($accountResponse['totalSize'] > 0) {
                                        $account = [
                                            'id' => $accountResponse['records'][0]['Id'],
                                            'rut' => $rutProveedor,
                                            'nombre' => $nombreProveedor,
                                            'area' => $licitacion['area'],
                                            'sector' => $licitacion['sector']
                                        ];
                                        $bancaEticaSalesforceClient->actualizarAccount($account);
                                    } else {
                                        $leadResponse = $bancaEticaSalesforceClient->obtenerLeadPorRut($rutProveedor);
                                        $lead = [
                                            'rut' => $rutProveedor,
                                            'proveedor' => $nombreProveedor,
                                            'area' => $licitacion['area'],
                                            'sector' => $licitacion['sector']
                                        ];

                                        if ($leadResponse['totalSize'] > 0) {
                                            $lead['id'] = $leadResponse['records'][0]['Id'];
                                            $bancaEticaSalesforceClient->actualizarLead($lead);
                                        } else {
                                            $lead['nombre'] = $this->salesforceSettings->default_firstname;
                                            $lead['apellido'] = $this->salesforceSettings->default_lastname;
                                            $lead['direccion'] = '';

                                            $addLeadResponse = $bancaEticaSalesforceClient->agregarLead($lead);
                                            $leadId = $addLeadResponse['id'];
                                        }
                                    }

                                    $eventoBiografico = [
                                        'codigo' => $licitacion['CodigoExterno'],
                                        'nombre' => $licitacion['Nombre'],
                                        'descripcion' => $licitacion['Descripcion'],
                                        'tipo' => $licitacion['Tipo'],
                                        'organismo' => $licitacion['Comprador']['NombreOrganismo'],
                                        'leadId' => $lead['id'],
                                        'accountId' => $account['id'],
                                        'recordTypeId' => $this->salesforceSettings->record_type_id,
                                        'fecha' => $licitacion['Adjudicacion']['Fecha'],
                                        'monto' => $item['Adjudicacion']['Cantidad'] * $item['Adjudicacion']['MontoUnitario'],
                                        'nombreEvento' => $this->salesforceSettings->default_biographical_event_name
                                    ];

                                    $bancaEticaSalesforceClient->agregarEventoBiografico($eventoBiografico);
                                } 
                            }
                        }

                        $licitacionesProcesadas[] = $licitacion;
                    }
                })
            ->onEnd(function(EndProcessEvent $event) use (&$licitacionesProcesadas) {
                Log::info('Ha concluido la ETL con ' . count($licitacionesProcesadas) . ' licitaciones filtradas.');
            })
            ->onLoadException(function(ItemExceptionEvent $e) {
                Log::error('Ha ocurrido un error al procesar licitacion ' . $e->getItem()['CodigoExterno'] . ': ' .$e->getException()->getMessage());
                $e->ignoreException();
            })
            ->createEtl();

        $etl->process($licitaciones);
        
        return $licitacionesProcesadas;
    }

    function obtenerConfiguraciones($sendToSalesforce) {
        $configuraciones = [];
        $configuraciones['sendToSalesforce'] = boolval($sendToSalesforce);
        $configuraciones['fecha'] = Carbon::yesterday()->format('dmY');

        $listaTipoLicitacionPermitidos = explode(';', $this->etlSettings->filtro_tipo_licitacion);
        $listaPalabrasExcluidas = explode(';', $this->etlSettings->filtro_palabras_excluidas);
        $listaPalabrasClaveEducacion = explode(';', $this->etlSettings->filtro_palabras_clave_educacion);
        $listaPalabrasClaveIndustriaCreativa = explode(';', $this->etlSettings->filtro_palabras_clave_industria_creativa);
        $listaPalabrasClaveTurismo = explode(';', $this->etlSettings->filtro_palabras_clave_turismo);
        $listaPalabrasClaveEspacioPublico = explode(';', $this->etlSettings->filtro_palabras_clave_espacio_publico);
        $listaPalabrasClaveDiseño = explode(';', $this->etlSettings->filtro_palabras_clave_diseño);
        $listaPalabrasClaveVialidad = explode(';', $this->etlSettings->filtro_palabras_clave_vialidad);
        $listaPalabrasClaveObrasPublicas = explode(';', $this->etlSettings->filtro_palabras_clave_obras_publicas);
        $listaPalabrasClaveSalud = explode(';', $this->etlSettings->filtro_palabras_clave_salud);
        $listaPalabrasClaveInclusion = explode(';', $this->etlSettings->filtro_palabras_clave_inclusion);
        $listaPalabrasClaveAgua = explode(';', $this->etlSettings->filtro_palabras_clave_agua);
        $listaPalabrasClaveApr = explode(';', $this->etlSettings->filtro_palabras_clave_apr);
        $listaPalabrasClaveSistemasAlimentarios = explode(';', $this->etlSettings->filtro_palabras_clave_sistemas_alimentarios);
        $listaPalabrasClaveProduccionSostenible = explode(';', $this->etlSettings->filtro_palabras_clave_produccion_sostenible);
        $listaPalabrasClaveEficienciaEnergetica = explode(';', $this->etlSettings->filtro_palabras_clave_eficiencia_energetica);
        $listaPalabrasExcluidasEducacionYCultura = explode(';', $this->etlSettings->filtro_palabras_excluidas_educacion_y_cultura);
        $listaPalabrasExcluidasDesarrolloSocial = explode(';', $this->etlSettings->filtro_palabras_excluidas_desarrollo_social);
        $listaPalabrasExcluidasMedioAmbiente = explode(';', $this->etlSettings->filtro_palabras_excluidas_medio_ambiente);

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

    private function bloquear($fp) {
        if (!flock($fp, LOCK_EX | LOCK_NB)) {
            fclose($fp);
            throw new RuntimeException('Una ejecución del proceso ya está en curso');
        }
    }

    private function desbloquear($fp) {
        flock($fp, LOCK_UN);
        fclose($fp);
    }
}