<?php

namespace App\Services\MercadoPublico\Clients;

use Carbon\Carbon;
use Omniphx\Forrest\Providers\Laravel\Facades\Forrest;

class BancaEticaSalesforceClient {
    public function autenticar() {
        Forrest::authenticate();
    }

    public function obtenerAccountPorRut($rut) {
        $this->autenticar();
        return Forrest::query('SELECT Id FROM Account WHERE DNI__c = \'' . $rut . '\'');
    }

    public function obtenerLeadPorRut($rut) {
        $this->autenticar();
        return Forrest::query('SELECT Id FROM LEAD WHERE DNI__c = \'' . $rut . '\'');
    }

    public function actualizarAccount($account) {
        $this->autenticar();
        return Forrest::sobjects('Account/' . $account['id'], [
            'method' => 'patch',
            'body' => [
                'DNI__c' => $account['rut'],
                'Company' => $account['nombre'],
                'Area__c' => $account['area'],
                'Industry' => $account['sector']
            ],
        ]);
    }

    public function actualizarLead($lead) {
        $this->autenticar();
        return Forrest::sobjects('Lead/' . $lead['id'], [
            'method' => 'patch',
            'body' => [
                'DNI__c' => $lead['rut'],
                'Company' => $lead['proveedor'],
                'Area__c' => $lead['area'],
                'Industry' => $lead['sector']
            ],
        ]);
    }

    public function agregarLead($lead) {
        $this->autenticar();
        return Forrest::sobjects('Lead', [
            'method' => 'post',
            'body' => [
                'FirstName' => $lead['nombre'],
                'LastName' => $lead['apellido'],
                'DNI__c' => $lead['rut'],
                'Company' => $lead['proveedor'],
                'Address__c' => $lead['direccion'],
                'Area__c' => $lead['area'],
                'Industry' => $lead['sector']
            ],
        ]);
    }

    public function agregarEventoBiografico($eventoBiografico) {
        $this->autenticar();
        return Forrest::sobjects('BiographicalEvent__c', [
            'method' => 'post',
            'body' => [
                'BidId__c' => $eventoBiografico['codigo'],
                'BidName__c' => $eventoBiografico['nombre'],
                'Description__c' => $eventoBiografico['descripcion'],
                'BidType__c' => $eventoBiografico['tipo'],
                'BidOrganization__c' => $eventoBiografico['organismo'],
                'Lead__c' => $eventoBiografico['leadId'],
                'Account__c' => $eventoBiografico['accountId'],
                'RecordTypeId' => $eventoBiografico['recordTypeId'],
                'Date__c' => Carbon::parse($eventoBiografico['fecha'])->format('Y-m-d'),
                'BidAmount__c' => $eventoBiografico['monto'],
                'Name' => $eventoBiografico['nombreEvento']
            ],
        ]);
    }
}
