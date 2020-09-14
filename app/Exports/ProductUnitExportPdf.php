<?php

namespace App\Exports;

use \App\ProductUnit;
use App\Components\Filters\ProductUnitFilter;
use Illuminate\Http\Request;
use \PDF;

class ProductUnitExportPdf
{
	public static function print($params = [], $fileName)
	{
		$filter = new ProductUnitFilter(new Request($params));
		$data   = ProductUnit::join('products', 'products.id', 'product_units.product_id')
			->leftJoin('units', 'units.id', 'product_units.unit_id')
			->select('product_units.conversion', 'product_units.price', 'products.name as product_name', 'units.name as unit_name')
			->filter($filter)->get();

		dirExists($fileName);

		$pdf    = PDF::loadView('components.pdf_template', [
			'data'   => $data,
			'header' => [
				['CONVERSION','text'],
				['PRICE','text'],
				['PRODUCT NAME','text'],
				['UNIT NAME','text']
			],
			'columns' => [
				'conversion', 'price', 'product_name', 'unit_name'
			],
			'modelName' => "ProductUnit"
		]);

        $pdf
	        ->setOptions(["isPhpEnabled"=> true, 'isRemoteEnabled'=>true])
	        ->setPaper('a4', 'potrait')
	        ->save(public_path($fileName));
	}
}