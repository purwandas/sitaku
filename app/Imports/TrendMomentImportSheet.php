<?php

namespace App\Imports;

use \App\TrendMoment;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TrendMomentImportSheet implements OnEachRow, WithHeadingRow
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

        

        $data = TrendMoment::firstOrCreate([
            'month_' => $row['month_'],
			'year_' => $row['year_'],
			'total_sales' => $row['total_sales']
        ]);

    }
}
