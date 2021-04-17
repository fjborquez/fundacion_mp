<?php 

namespace App\Services\MercadoPublico\Clients;

use App\Services\MercadoPublico\Settings\MercadoPublicoSettings;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use DomainException;
use Exception;

class MercadoPublicoHttpClient {
    private $settings;

    function __construct() {
        $this->settings = app(MercadoPublicoSettings::class);
    }

    function obtenerLicitacionesConDetalles($fecha) {
        $licitacionesConDetalles = [];
        $listaLicitaciones = $this->obtenerListaLicitaciones($fecha);

        foreach($listaLicitaciones as $licitacionEnLista) {
            sleep($this->settings->segundos_entre_consultas);

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
        $response = Http::retry($this->settings->retry, $this->settings->milisegundos_entre_consultas)->get($this->settings->url_licitaciones, [
            'fecha' => $fecha,
            'ticket' => $this->settings->ticket
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
        $response = Http::retry($this->settings->retry, $this->settings->milisegundos_entre_consultas)->get($this->settings->url_licitaciones, [
            'ticket' => $this->settings->ticket,
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