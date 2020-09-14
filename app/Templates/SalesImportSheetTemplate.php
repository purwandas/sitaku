<?php

namespace App\Templates;

use App\Templates\SalesImportInputTemplate;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SalesImportSheetTemplate implements WithMultipleSheets
{
    use Exportable;

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets  = [];
        $foreign = ['SalesImportDataSupplierTemplate'];

        $sheets[] = new SalesImportInputTemplate();

        foreach ($foreign as $key => $value) {
            $class    = "\\App\\Templates\\".$value;
            $sheets[] = new $class();
        }

        return $sheets;
    }
}