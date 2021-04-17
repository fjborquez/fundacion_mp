<?php

namespace App\Services\MercadoPublico\Etl;

use App\Services\MercadoPublico\Settings\SalesforceSettings;
use App\Services\MercadoPublico\Settings\EtlSettings;
use App\Services\MercadoPublico\Clients\BancaEticaSalesforceClient;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Exception;
use DomainException;

class EtlHelper {
    private $salesforceSettings;
    private $etlSettings;
    private $listasPalabras;

    function __construct() {
        $this->salesforceSettings = app(SalesforceSettings::class);
        $this->etlSettings = app(EtlSettings::class);
        $this->listasPalabras = $this->generarListasPalabras();
    }

    function generarListasPalabras() {
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

        return [
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
    }

    function isFormatoLicitacionValido($licitacion) {
        if (!Arr::exists($licitacion, 'Adjudicacion') || $licitacion['Adjudicacion'] == null) {
            return false;
        }

        if (!Arr::exists($licitacion, 'Items') || empty($licitacion['Items'])) {
            return false;
        }

        return true;
    }

    function filtrarPorTipoLicitacion($licitacion) {
        if (!in_array($licitacion['Tipo'], $this->listasPalabras['listaTipoLicitacionPermitidos'])) {
            return false;
        }

        return true;
    }

    function filtrarPorPalabrasExcluidasNombreLicitacion($licitacion) {
        if (Str::of($licitacion['Nombre'])->contains($this->listasPalabras['listaPalabrasExcluidas'])) {
            return false;
        }

        return true;
    }

    function categorizarLicitacion(&$licitacion) {
        // TODO: Pasar textos de area/sector a constante u otro
        if (Str::of($licitacion['Nombre'])->contains($this->listasPalabras['educacion'])) {
            $licitacion['area'] = 'educación y cultura';
            $licitacion['sector'] = 'educación';
        } elseif (Str::of($licitacion['Nombre'])->contains($this->listasPalabras['industriaCreativa'])) {
            $licitacion['area'] = 'educación y cultura';
            $licitacion['sector'] = 'industria creativa';
        } elseif (Str::of($licitacion['Nombre'])->contains($this->listasPalabras['turismo'])) {
            $licitacion['area'] = 'educación y cultura';
            $licitacion['sector'] = 'turismo';
        } elseif (Str::of($licitacion['Nombre'])->contains($this->listasPalabras['espacioPublico'])) {
            $licitacion['area'] = 'desarrollo social';
            $licitacion['sector'] = 'espacio público';
        } elseif (Str::of($licitacion['Nombre'])->contains($this->listasPalabras['diseño'])) {
            $licitacion['area'] = 'desarrollo social';
            $licitacion['sector'] = 'diseño';
        } elseif (Str::of($licitacion['Nombre'])->contains($this->listasPalabras['vialidad'])) {
            $licitacion['area'] = 'desarrollo social';
            $licitacion['sector'] = 'vialidad';
        } elseif (Str::of($licitacion['Nombre'])->contains($this->listasPalabras['obrasPublicas'])) {
            $licitacion['area'] = 'desarrollo social';
            $licitacion['sector'] = 'obras públicas';
        } elseif (Str::of($licitacion['Nombre'])->contains($this->listasPalabras['salud'])) {
            $licitacion['area'] = 'desarrollo social';
            $licitacion['sector'] = 'salud';
        } elseif (Str::of($licitacion['Nombre'])->contains($this->listasPalabras['inclusion'])) {
            $licitacion['area'] = 'desarrollo social';
            $licitacion['sector'] = 'inclusión';
        } elseif (Str::of($licitacion['Nombre'])->contains($this->listasPalabras['agua'])) {
            $licitacion['area'] = 'medio ambiente';
            $licitacion['sector'] = 'agua';
        } elseif (Str::of($licitacion['Nombre'])->contains($this->listasPalabras['apr'])) {
            $licitacion['area'] = 'medio ambiente';
            $licitacion['sector'] = 'apr';
        } elseif (Str::of($licitacion['Nombre'])->contains($this->listasPalabras['sistemaAlimentarios'])) {
            $licitacion['area'] = 'medio ambiente';
            $licitacion['sector'] = 'sistemas alimentarios';
        } elseif (Str::of($licitacion['Nombre'])->contains($this->listasPalabras['produccionSostenible'])) {
            $licitacion['area'] = 'medio ambiente';
            $licitacion['sector'] = 'producción sostenible';
        } elseif (Str::of($licitacion['Nombre'])->contains($this->listasPalabras['eficienciaEnergetica'])) {
            $licitacion['area'] = 'medio ambiente';
            $licitacion['sector'] = 'eficiencia energética';
        } else {
            return false;
        }

        return true;
    }

    function filtrarPorNombreLicitacionExcluidosCategoria($licitacion) {
        // TODO: Pasar textos de area/sector a constante u otro
        if ($licitacion['area'] == 'educación y cultura' 
            && Str::of($licitacion['Nombre'])->contains($this->listasPalabras['excluidasEducacionYCultura'])) {
            return false;
        }

        if ($licitacion['area'] == 'desarrollo social' 
            && Str::of($licitacion['Nombre'])->contains($this->listasPalabras['excluidasDesarrolloSocial'])) {
            return false;
        }

        if ($licitacion['area'] == 'medio ambiente' 
            && Str::of($licitacion['Nombre'])->contains($this->listasPalabras['excluidasMedioAmbiente'])) {
            return false;
        }

        return true;
    }

    function enviarAdjudicacionesASalesforce($licitacion) {
        foreach($licitacion['Items']['Listado'] as $item) {
            try {
                if (!Arr::exists($item, 'Adjudicacion')) {
                    throw new DomainException('El Item no cumple formato para envio a Salesforce');
                }
    
                $this->enviarAdjudicacionASalesforce($licitacion, $item['Adjudicacion']);
            } catch (Exception $e) {
                Log::notice('Ha ocurrido un problema al intentar enviar a Salesforce adjudicacion ' . $item['Correlativo'] . ' de la licitacion ' . $licitacion['CodigoExterno'] . ': ' . $e->getMessage());
            }
        }
    }

    function enviarAdjudicacionASalesforce($licitacion, $adjudicacion) {
        if (!is_array($adjudicacion)) {
            throw new DomainException('La adjudicacion no cumple formato para envio a Salesforce');
        }

        // TODO: Funcion para formatear rut
        $rutProveedor = str_replace('.', '', $adjudicacion['RutProveedor']);
        $nombreProveedor = $adjudicacion['NombreProveedor'];

        $bancaEticaSalesforceClient = new BancaEticaSalesforceClient();
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
            'monto' => $adjudicacion['Cantidad'] * $adjudicacion['MontoUnitario'],
            'nombreEvento' => $this->salesforceSettings->default_biographical_event_name
        ];

        $bancaEticaSalesforceClient->agregarEventoBiografico($eventoBiografico);
    }
}
