<?php

namespace App\Imports;

use \App\Supplier;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SupplierImportSheet implements OnEachRow, WithHeadingRow
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

        $production = \App\Production::where('name',$row['production_name'])->first();

        $data = Supplier::firstOrCreate([
            'name' => $row['name'],
			'address' => $row['address'],
			'phone' => $row['phone'],
			'production_id' => $production->id
        ]);

    }
}
