<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PurchaseImport implements WithMultipleSheets 
{
   
    public function sheets(): array
    {
        return [
            0 => new PurchaseImportSheet(),
        ];
    }
}