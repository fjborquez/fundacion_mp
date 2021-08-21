<?php

namespace App\Services\MercadoPublico\Helpers;

use Carbon\Carbon;
use Excel;

class CsvHelper {
    public function generarArchivo($licitaciones, $fecha) {
        $licitacionesCsv = $this->filtrarCampos($licitaciones);
        $fechaCreacion = Carbon::now()->format('Y-m-d\TH-i-s');
        Excel::store(new AdjudicacionExport($licitacionesCsv), 'csv/' . $fecha . '_' . $fechaCreacion . '.csv');
    }

    private function filtrarCampos($licitaciones) {
        $licitacionesCsv = [];
        $index = 0;

        foreach ($licitaciones as $licitacion) {
            $adjudicaciones = $this->obtenerAdjudicaciones($licitacion, $index);
            $licitacionesCsv = array_merge($licitacionesCsv, $adjudicaciones);
        }

        return $licitacionesCsv;
    }

    private function obtenerAdjudicaciones($licitacion, &$index) {
        $adjudicaciones = [];

        foreach ($licitacion['Items']['Listado'] as $item) {
            $adjudicacion = $item['Adjudicacion'];

            if ($adjudicacion != null) {
                $adjudicaciones[] = [
                    ++$index,
                    $adjudicacion['NombreProveedor'],
                    $adjudicacion['RutProveedor'],
                    $licitacion['CodigoExterno'],
                    $licitacion['Nombre'],
                    $licitacion['Descripcion'],
                    $licitacion['Adjudicacion']['Fecha'],
                    $licitacion['Tipo'],
                    $licitacion['Comprador']['NombreOrganismo'],
                    $adjudicacion['Cantidad'] * $adjudicacion['MontoUnitario'],
                ];
            }
        }

        return $adjudicaciones;
    } 
}
