<?php

namespace App\Imports;

use \App\Purchase;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PurchaseImportSheet implements OnEachRow, WithHeadingRow
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

        $user = \App\User::where('name',$row['user_name'])->first();
		$supplier = \App\Supplier::where('name',$row['supplier_name'])->first();

        $data = Purchase::firstOrCreate([
            'user_id' => $user->id,
			'supplier_id' => $supplier->id,
			'date' => $row['date'],
			'total_payment' => $row['total_payment'],
			'total_paid' => $row['total_paid'],
			'total_change' => $row['total_change']
        ]);

    }
}
