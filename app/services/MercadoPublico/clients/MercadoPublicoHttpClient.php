<?php 

namespace App\Services\MercadoPublico\Clients;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use DomainException;
use Exception;

class MercadoPublicoHttpClient {
    private $configuraciones;

    function __construct($configuraciones) {
        $this->configuraciones = $configuraciones;
    }

    function obtenerLicitacionesConDetalles($fecha) {
        $licitacionesConDetalles = [];
        $listaLicitaciones = $this->obtenerListaLicitaciones($fecha);

        foreach($listaLicitaciones as $licitacionEnLista) {
            sleep($this->configuraciones['segundos_entre_consultas']);

            try {
                $licitacionesConDetalles[] = $this->obtenerDetalleLicitacion($licitacionEnLista['CodigoExterno']);
            } catch (Exception $e) {
                Log::error('Error al consultar por licitacion ' . $licitacionEnLista['CodigoExterno'] . ': ' . $e->getMessage());
            }
        }

        Log::info('Se encontraron ' . count($licitacionesConDetalles) . ' licitaciones con detalles del día ' . $fecha . '.');

        return $licitacionesConDetalles;
    }
    
    // Obtener listado de licitaciones sin detalles
    function obtenerListaLicitaciones($fecha) {
        $response = Http::retry($this->configuraciones['retry'], $this->configuraciones['milisegundos_entre_consultas'])->get($this->configuraciones['url'], [
            'fecha' => $fecha,
            'ticket' => $this->configuraciones['ticket']
        ]);

        if (!$response->successful()) {
            $response->throw();
        }

        $licitaciones = $response->collect();

        if (!$licitaciones->has('Listado')) {
            throw new DomainException('No hay campo Listado al consultar por todas las licitaciones');
        }

        Log::info('Se encontraron ' . $licitaciones->get('Cantidad') . ' licitaciones en listado del día ' . $fecha . '.');

        return $licitaciones->get('Listado');
    }

    // Obtener detalles de una licitación
    function obtenerDetalleLicitacion($codigoExterno) {
        $response = Http::retry($this->configuraciones['retry'], $this->configuraciones['milisegundos_entre_consultas'])->get($this->configuraciones['url'], [
            'ticket' => $this->configuraciones['ticket'],
            'codigo' => $codigoExterno
        ]);

        if (!$response->successful()) {
            $response->throw();
        }

        $licitacion = $response->collect();

        if (!$licitacion->has('Listado')) {
            throw new DomainException('No hay campo Listado al consultar por detalle de licitacion ' . $codigoExterno);
        }

        return $response->collect()->get('Listado')[0];
    }

}