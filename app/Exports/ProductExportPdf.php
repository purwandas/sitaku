<?php

namespace App\Exports;

use \App\Product;
use App\Components\Filters\ProductFilter;
use Illuminate\Http\Request;
use \PDF;

class ProductExportPdf
{
	public static function print($params = [], $fileName)
	{
		$filter = new ProductFilter(new Request($params));
		$data   = Product::join('categories', 'categories.id', 'products.category_id')
			->join('productions', 'productions.id', 'products.production_id')
			->select('products.name', 'products.stock', 'products.buying_price', 'products.selling_price', 'categories.name as category_name', 'productions.name as production_name')
			->filter($filter)->get();

		dirExists($fileName);

		$pdf    = PDF::loadView('components.pdf_template', [
			'data'   => $data,
			'header' => [
				['NAME','text'],
				['STOCK','number'],
				['BUYING PRICE','number'],
				['SELLING PRICE','number'],
				['CATEGORY NAME','text'],
				['PRODUCTION NAME','text']
			],
			'columns' => [
				'name', 'stock', 'buying_price', 'selling_price', 'category_name', 'production_name'
			],
			'modelName' => "Product"
		]);

        $pdf
	        ->setOptions(["isPhpEnabled"=> true, 'isRemoteEnabled'=>true])
	        ->setPaper('a4', 'landscape')
	        ->save(public_path($fileName));
	}
}