<?php

namespace App\Services\MercadoPublico;

use App\Services\MercadoPublico\Clients\MercadoPublicoHttpClient;
use App\Services\MercadoPublico\Etl\EtlHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use BenTools\ETL\Etl;
use BenTools\ETL\EtlBuilder;
use BenTools\ETL\Transformer\CallableTransformer;
use BenTools\ETL\EventDispatcher\Event\EndProcessEvent;
use BenTools\ETL\EventDispatcher\Event\ItemExceptionEvent;
use RuntimeException;
use DomainException;

class MercadoPublicoETL {
    private $etlHelper;

    public function __construct() {
        $this->etlHelper = new EtlHelper();
    }

    public function generarETL($sendToSalesforce = false) {
        $licitaciones = [];
        // TODO: Externalizar bloqueo
        $file = fopen(storage_path('framework/locks/etlmp.txt'), "r+");
        
        $this->bloquear($file);
        $licitaciones = $this->ejecutar(boolval($sendToSalesforce));
        $this->desbloquear($file);
        
        return $licitaciones;
    }

    public function ejecutar($sendToSalesforce = false) {
        Log::info('Ha iniciado el proceso de ETL');

        $licitacionesProcesadas = [];
        $mercadoPublicoHttpClient = new MercadoPublicoHttpClient();
        $fecha = Carbon::yesterday()->format('dmY');
        $licitaciones = $mercadoPublicoHttpClient->obtenerLicitacionesConDetalles($fecha);
        
        Log::info('Enviar licitaciones a Salesforce: ' . var_export($sendToSalesforce, true));

        // TODO: Refactorizar funciones de etl
        $etl = EtlBuilder::init()
            ->transformWith(function($item) {
                array_walk_recursive($item, function (&$value) {
                    $value = mb_convert_encoding(Str::lower($value), "UTF-8");
                });
        
                yield $item;
            })
            ->loadInto(
                function ($generated, $key, Etl $etl) use (&$licitacionesProcesadas, $sendToSalesforce) {
                    foreach ($generated as $licitacion) {
                        if (!$this->etlHelper->isFormatoLicitacionValido($licitacion)) {
                            throw new DomainException('La licitación no cumple con el formato valido para la ETL.');
                        }

                        if (!$this->etlHelper->filtrarPorTipoLicitacion($licitacion)) {
                            $etl->skipCurrentItem();
                            break;
                        }

                        if (!$this->etlHelper->filtrarPorPalabrasExcluidasNombreLicitacion($licitacion)) {
                            $etl->skipCurrentItem();
                            break;
                        }

                        if (!$this->etlHelper->categorizarLicitacion($licitacion)) {
                            $etl->skipCurrentItem();
                            break;
                        }

                        if (!$this->etlHelper->filtrarPorNombreLicitacionExcluidosCategoria($licitacion)) {
                            $etl->skipCurrentItem();
                            break;
                        }

                        if ($sendToSalesforce) {
                            $this->etlHelper->enviarAdjudicacionesASalesforce($licitacion);
                        }

                        $licitacionesProcesadas[] = $licitacion;
                    }
                })
            ->onEnd(function(EndProcessEvent $event) use (&$licitacionesProcesadas) {
                Log::info('Ha concluido la ETL con ' . count($licitacionesProcesadas) . ' licitaciones filtradas.');
            })
            ->onLoadException(function(ItemExceptionEvent $exception) {
                Log::error('Ha ocurrido un error al procesar licitacion ' . $exception->getItem()['CodigoExterno'] . ': ' . $exception->getException()->getMessage());
                $exception->ignoreException();
            })
            ->createEtl();

        $etl->process($licitaciones);
        
        return $licitacionesProcesadas;
    }

    // FUNCIONES DE MUTEX
    private function bloquear($file) {
        if (!flock($file, LOCK_EX | LOCK_NB)) {
            fclose($file);
            throw new RuntimeException('Una ejecución del proceso ya está en curso');
        }
    }

    private function desbloquear($file) {
        flock($file, LOCK_UN);
        fclose($file);
    }
}