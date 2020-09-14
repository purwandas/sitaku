<?php

namespace App\Imports;

use \App\Sales;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SalesImportSheet implements OnEachRow, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

    public function onRow(Row $row)
    {
        $rowIndex = $row->getIndex();
        $row = $row->toArray();

        $supplier = \App\Supplier::where('name',$row['supplier_name'])->first();

        $data = Sales::firstOrCreate([
            'date' => $row['date'],
			'supplier_id' => $supplier->id
        ]);

    }
}
