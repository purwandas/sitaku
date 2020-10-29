<?php

namespace App\Templates;

use App\Templates\PurchaseImportInputTemplate;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PurchaseImportSheetTemplate implements WithMultipleSheets
{
    use Exportable;

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets  = [];
        $foreign = ['PurchaseImportDataUserTemplate','PurchaseImportDataSupplierTemplate'];

        $sheets[] = new PurchaseImportInputTemplate();

        foreach ($foreign as $key => $value) {
            $class    = "\\App\\Templates\\".$value;
            $sheets[] = new $class();
        }

        return $sheets;
    }
}