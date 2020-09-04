<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ProductImport implements WithMultipleSheets 
{
   
    public function sheets(): array
    {
        return [
            0 => new ProductImportSheet(),
        ];
    }
}