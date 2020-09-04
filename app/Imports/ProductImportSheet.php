<?php

namespace App\Imports;

use \App\Product;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductImportSheet implements OnEachRow, WithHeadingRow
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

        $category = \App\Category::where('name',$row['category_name'])->first();
		$production = \App\Production::where('name',$row['production_name'])->first();

        $data = Product::firstOrCreate([
            'name' => $row['name'],
			'stock' => $row['stock'],
			'buying_price' => $row['buying_price'],
			'selling_price' => $row['selling_price'],
			'category_id' => $category->id,
			'production_id' => $production->id
        ]);

    }
}
