<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class UnitImport implements WithMultipleSheets 
{
   
    public function sheets(): array
    {
        return [
            0 => new UnitImportSheet(),
        ];
    }
}