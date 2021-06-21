<?php

namespace App\Services\MercadoPublico\Helpers;

use App\Services\MercadoPublico\Clients\BancaEticaSalesforceClient;
use App\Services\MercadoPublico\Settings\SalesforceSettings;

use DomainException;
use Illuminate\Support\Arr;

class SalesforceHelper {
    private $salesforceSettings;

    public function __construct() {
        $this->salesforceSettings = app(SalesforceSettings::class);
    }

    public function enviarLicitacionesASalesforce($licitaciones = [], $sendToSalesforce = false) {
        if ($sendToSalesforce) {
            foreach($licitaciones as $licitacion) {
                $this->enviarAdjudicacionesASalesforce($licitacion);
            }
        }
    }

    public function enviarAdjudicacionesASalesforce($licitacion) {
        foreach($licitacion['Items']['Listado'] as $item) {
            $this->enviarAdjudicacionASalesforce($licitacion, $item['Adjudicacion']);
        }
    }

    public function enviarAdjudicacionASalesforce($licitacion, $adjudicacion) {
        $rutProveedor = $adjudicacion['RutProveedor'];
        $nombreProveedor = $adjudicacion['NombreProveedor'];

        $bancaEticaSalesforceClient = new BancaEticaSalesforceClient();
        $accountResponse = $bancaEticaSalesforceClient->obtenerAccountPorRut($rutProveedor);

        $account = [
            'id' => '',
        ];
        
        $lead = [
            'id' => '',
        ];

        if ($accountResponse['totalSize'] > 0) {
            $account = [
                'id' => $accountResponse['records'][0]['Id'],
                'rut' => $rutProveedor,
                'nombre' => $nombreProveedor,
                'area' => $licitacion['area'],
                'sector' => $licitacion['sector'],
            ];
            $bancaEticaSalesforceClient->actualizarAccount($account);
        } else {
            $leadResponse = $bancaEticaSalesforceClient->obtenerLeadPorRut($rutProveedor);
            $lead = [
                'rut' => $rutProveedor,
                'proveedor' => $nombreProveedor,
                'area' => $licitacion['area'],
                'sector' => $licitacion['sector'],
            ];

            if ($leadResponse['totalSize'] > 0) {
                $lead['id'] = $leadResponse['records'][0]['Id'];
                $bancaEticaSalesforceClient->actualizarLead($lead);
            } else {
                $lead['nombre'] = $this->salesforceSettings->default_firstname;
                $lead['apellido'] = $this->salesforceSettings->default_lastname;
                $lead['direccion'] = '';

                $addLeadResponse = $bancaEticaSalesforceClient->agregarLead($lead);
                $lead['id'] = $addLeadResponse['id'];
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
            'monto' => $adjudicacion['MontoTotal'],
            'nombreEvento' => $this->salesforceSettings->default_biographical_event_name,
            'tramo_monto' => $adjudicacion['TramoMonto'],
        ];

        $bancaEticaSalesforceClient->agregarEventoBiografico($eventoBiografico);
    }
}
