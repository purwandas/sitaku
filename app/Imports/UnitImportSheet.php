<?php

namespace App\Imports;

use \App\Unit;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UnitImportSheet implements OnEachRow, WithHeadingRow
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

        

        $data = Unit::firstOrCreate([
            'name' => $row['name'],
			'conversion' => $row['conversion']
        ]);

    }
}
