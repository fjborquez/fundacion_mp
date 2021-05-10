<?php

namespace App\Services\MercadoPublico\Helpers;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AdjudicacionExport implements FromArray, WithHeadings {
    private $licitaciones;

    public function __construct(array $adjudicaciones) {
        $this->adjudicaciones = $adjudicaciones;
    }

    public function headings(): array {
        return [
            '#',
            'Razón Social',
            'RUT',
            'Id Licitación',
            'Nombre licitación',
            'Descripción',
            'Fecha',
            'Tipo',
            'Organismo Licitador',
            'Monto licitación',
        ];
    }

    public function columnFormats(): array {
        return [
            'A' => DataType::TYPE_STRING,
        ];
    }

    public function array(): array {
        return $this->adjudicaciones;
    }
}
