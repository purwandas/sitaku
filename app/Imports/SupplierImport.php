<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SupplierImport implements WithMultipleSheets 
{
   
    public function sheets(): array
    {
        return [
            0 => new SupplierImportSheet(),
        ];
    }
}