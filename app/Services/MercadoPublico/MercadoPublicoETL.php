<?php

namespace App\Services\MercadoPublico;

use App\Services\MercadoPublico\Clients\MercadoPublicoHttpClient;
use App\Services\MercadoPublico\Helpers\ClassLoaderHelper;
use App\Services\MercadoPublico\Helpers\CsvHelper;
use App\Services\MercadoPublico\Helpers\EtlHelper;
use App\Services\MercadoPublico\Helpers\MailHelper;
use App\Services\MercadoPublico\Helpers\SalesforceHelper;
use App\Services\MercadoPublico\Mutex\Mutex;
use BenTools\ETL\Etl;
use BenTools\ETL\EtlBuilder;
use BenTools\ETL\EventDispatcher\Event\EndProcessEvent;
use BenTools\ETL\EventDispatcher\Event\ItemExceptionEvent;
use Carbon\Carbon;
use CodeInc\StripAccents\StripAccents;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MercadoPublicoETL {
    private $etlHelper;
    private $mutex;
    private $salesforceHelper;
    private $filtros;
    private $modificadores;
    private $csvHelper;
    private $classLoaderHelper;
    private $mailHelper;

    public function __construct() {
        $this->etlHelper = new EtlHelper();
        $this->mutex = new Mutex();
        $this->salesforceHelper = new SalesforceHelper();
        $this->csvHelper = new CsvHelper();
        $this->classLoaderHelper = new ClassLoaderHelper();
        $this->mailHelper = new MailHelper();
        $this->filtros = [
            'premodificadores' => $this->classLoaderHelper->loadClasses($this->classLoaderHelper->getClasses('premodificadores'), 'App\\Services\\MercadoPublico\\Filtros\\'), 
            'postmodificadores' => $this->classLoaderHelper->loadClasses($this->classLoaderHelper->getClasses('postmodificadores'), 'App\\Services\\MercadoPublico\\Filtros\\'),
        ];
        $this->modificadores = $this->classLoaderHelper->loadClasses($this->classLoaderHelper->getClasses('modificadores'), 'App\\Services\\MercadoPublico\\Modificadores\\');
        $this->validadores = $this->classLoaderHelper->loadClasses($this->classLoaderHelper->getClasses('validadores'), 'App\\Services\\MercadoPublico\\Modificadores\\');
    }

    public function generarETL($sendToSalesforce = false, $sendToMail = false) {
        $licitaciones = [];
        
        $this->mutex->bloquear();
        $licitaciones = $this->ejecutar(boolval($sendToSalesforce), boolval($sendToMail));
        $this->mutex->desbloquear();
        
        return $licitaciones;
    }

    public function ejecutar($sendToSalesforce = false, $sendToMail = false) {
        Log::info('Ha iniciado el proceso de ETL');

        $licitacionesProcesadas = [];
        $licitacionesConProblemas = [];
        $mercadoPublicoHttpClient = new MercadoPublicoHttpClient();
        $fecha = Carbon::yesterday()->format('dmY');
        $licitaciones = $mercadoPublicoHttpClient->obtenerLicitacionesConDetalles($fecha);
        
        Log::info('Enviar licitaciones a Salesforce: ' . var_export($sendToSalesforce, true));

        // TODO: Refactorizar funciones de etl
        $etl = EtlBuilder::init()
            ->transformWith(function($item) {
                array_walk_recursive($item, function (&$value) {
                    $value = trim(StripAccents::strip(mb_convert_encoding(Str::lower($value), 'UTF-8')));
                });
        
                yield $item;
            })
            ->loadInto(
                function ($generated, $key, Etl $etl) use (&$licitacionesProcesadas) {
                    foreach ($generated as $licitacion) {
                        $this->etlHelper->aplicarValidadores($licitacion, $this->validadores, $etl);
                        $this->etlHelper->aplicarFiltros($licitacion, $this->filtros['premodificadores'], $etl);
                        $this->etlHelper->aplicarModificadores($licitacion, $this->modificadores, $etl);
                        $this->etlHelper->aplicarFiltros($licitacion, $this->filtros['postmodificadores'], $etl);

                        $licitacionesProcesadas[] = $licitacion;
                    }
                })
            ->onEnd(function(EndProcessEvent $event) use (&$licitacionesProcesadas, &$licitacionesConProblemas, $fecha, $sendToSalesforce, $sendToMail) {
                $this->csvHelper->generarArchivo($licitacionesConProblemas, $fecha);
                $this->salesforceHelper->enviarLicitacionesASalesforce($licitacionesProcesadas, $sendToSalesforce);
                $this->mailHelper->enviarLicitacionesAMail($licitacionesProcesadas, $sendToMail);
                
                Log::info('Ha concluido la ETL con ' . count($licitacionesProcesadas) . ' licitaciones filtradas.');
            })
            ->onLoadException(function(ItemExceptionEvent $exception) use (&$licitacionesConProblemas) {
                Log::error('Ha ocurrido un error al procesar licitacion ' . $exception->getItem()['CodigoExterno'] . ': ' . $exception->getException()->getMessage());
                $licitacionesConProblemas[] = $exception->getItem();
                $exception->ignoreException();
            })
            ->createEtl();

        $etl->process($licitaciones);
        
        return $licitacionesProcesadas;
    }
}
