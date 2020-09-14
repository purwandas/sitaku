<?php

namespace App\Templates;

use App\Templates\ProductUnitImportInputTemplate;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ProductUnitImportSheetTemplate implements WithMultipleSheets
{
    use Exportable;

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets  = [];
        $foreign = ['ProductUnitImportDataProductTemplate','ProductUnitImportDataUnitTemplate'];

        $sheets[] = new ProductUnitImportInputTemplate();

        foreach ($foreign as $key => $value) {
            $class    = "\\App\\Templates\\".$value;
            $sheets[] = new $class();
        }

        return $sheets;
    }
}