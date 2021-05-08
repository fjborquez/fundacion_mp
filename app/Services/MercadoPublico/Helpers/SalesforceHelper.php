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

    public function enviarAdjudicacionesASalesforce($licitacion) {
        foreach($licitacion['Items']['Listado'] as $item) {
            try {
                // TODO: Crear validador
                if (!Arr::exists($item, 'Adjudicacion')) {
                    throw new DomainException('El Item no cumple formato para envio a Salesforce');
                }
    
                $this->enviarAdjudicacionASalesforce($licitacion, $item['Adjudicacion']);
            } catch (Exception $exception) {
                Log::notice('Ha ocurrido un problema al intentar enviar a Salesforce adjudicacion ' . $item['Correlativo'] . ' de la licitacion ' . $licitacion['CodigoExterno'] . ': ' . $exception->getMessage());
            }
        }
    }

    public function enviarAdjudicacionASalesforce($licitacion, $adjudicacion) {
        // TODO: Crear validador
        if (!is_array($adjudicacion)) {
            throw new DomainException('La adjudicacion no cumple formato para envio a Salesforce');
        }

        // TODO: Pasar a modificador
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

        // TODO: Pasar a modficiador
        $monto = $adjudicacion['Cantidad'] * $adjudicacion['MontoUnitario'];
        $tramo_monto = 'B1';

        if ($monto > 100000000) {
            $tramo_monto = 'A1';
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
            'monto' => $adjudicacion['Cantidad'] * $adjudicacion['MontoUnitario'],
            'nombreEvento' => $this->salesforceSettings->default_biographical_event_name,
            'tramo_monto' => $tramo_monto,
        ];

        $bancaEticaSalesforceClient->agregarEventoBiografico($eventoBiografico);
    }
}
