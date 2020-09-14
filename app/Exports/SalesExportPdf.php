<?php

namespace App\Exports;

use \App\Sales;
use App\Components\Filters\SalesFilter;
use Illuminate\Http\Request;
use \PDF;

class SalesExportPdf
{
	public static function print($params = [], $fileName)
	{
		$filter = new SalesFilter(new Request($params));
		$data   = Sales::join('suppliers', 'suppliers.id', 'sales.supplier_id')
			->select('sales.date', 'suppliers.name as supplier_name')
			->filter($filter)->get();

		dirExists($fileName);

		$pdf    = PDF::loadView('components.pdf_template', [
			'data'   => $data,
			'header' => [
				['DATE','-'],
				['SUPPLIER NAME','text']
			],
			'columns' => [
				'date', 'supplier_name'
			],
			'modelName' => "Sales"
		]);

        $pdf
	        ->setOptions(["isPhpEnabled"=> true, 'isRemoteEnabled'=>true])
	        ->setPaper('a4', 'potrait')
	        ->save(public_path($fileName));
	}
}