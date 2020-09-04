<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RoleImport implements WithMultipleSheets 
{
   
    public function sheets(): array
    {
        return [
            0 => new RoleImportSheet(),
        ];
    }
}