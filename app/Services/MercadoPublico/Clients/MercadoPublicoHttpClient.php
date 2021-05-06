<?php 

namespace App\Services\MercadoPublico\Clients;

use App\Services\MercadoPublico\Settings\MercadoPublicoSettings;
use DomainException;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MercadoPublicoHttpClient {
    private $settings;

    public function __construct() {
        $this->settings = app(MercadoPublicoSettings::class);
    }

    public function obtenerLicitacionesConDetalles($fecha) {
        $licitacionesConDetalles = [];
        $listaLicitaciones = $this->obtenerListaLicitaciones($fecha);

        foreach($listaLicitaciones as $licitacionEnLista) {
            sleep($this->settings->segundos_entre_consultas);

            try {
                $licitacionesConDetalles[] = $this->obtenerDetalleLicitacion($licitacionEnLista['CodigoExterno']);
            } catch (Exception $exception) {
                Log::error('Error al consultar por licitacion ' . $licitacionEnLista['CodigoExterno'] . ': ' . $exception->getMessage());
            }
        }

        Log::info('Se encontraron ' . count($licitacionesConDetalles) . ' licitaciones con detalles del día ' . $fecha . '.');

        return $licitacionesConDetalles;
    }
    
    // Obtener listado de licitaciones sin detalles
    public function obtenerListaLicitaciones($fecha) {
        $params = [
            'fecha' => $fecha,
            'ticket' => $this->settings->ticket,
        ];

        $licitaciones = $this->consultar($params);

        Log::info('Se encontraron ' . $licitaciones->get('Cantidad') . ' licitaciones en listado del día ' . $fecha . '.');

        return $licitaciones->get('Listado');
    }

    // Obtener detalles de una licitación
    public function obtenerDetalleLicitacion($codigoExterno) {
        $params = [
            'ticket' => $this->settings->ticket,
            'codigo' => $codigoExterno,
        ];

        return $this->consultar($params)->collect()->get('Listado')[0];
    }

    private function consultar($params) {
        $response = Http::retry(
            $this->settings->retry,
            $this->settings->milisegundos_entre_consultas)->get($this->settings->url_licitaciones,
            $params
        );

        if (!$response->successful()) {
            $response->throw();
        }

        $licitaciones = $response->collect();

        if (!$licitaciones->has('Listado')) {
            throw new DomainException('No hay campo Listado al consultar por licitaciones');
        }

        return $licitaciones;
    }

}
