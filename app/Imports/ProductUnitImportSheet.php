<?php

namespace App\Imports;

use \App\ProductUnit;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductUnitImportSheet implements OnEachRow, WithHeadingRow
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

        $product = \App\Product::where('name',$row['product_name'])->first();
		$unit = \App\Unit::where('name',$row['unit_name'])->first();

        $data = ProductUnit::firstOrCreate([
            'conversion' => $row['conversion'],
			'price' => $row['price'],
			'product_id' => $product->id,
			'unit_id' => $unit->id
        ]);

    }
}
