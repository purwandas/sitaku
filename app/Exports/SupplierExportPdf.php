<?php

namespace App\Exports;

use \App\Supplier;
use App\Components\Filters\SupplierFilter;
use Illuminate\Http\Request;
use \PDF;

class SupplierExportPdf
{
	public static function print($params = [], $fileName)
	{
		$filter = new SupplierFilter(new Request($params));
		$data   = Supplier::join('productions', 'productions.id', 'suppliers.production_id')
			->select('suppliers.name', 'suppliers.address', 'suppliers.phone', 'productions.name as production_name')
			->filter($filter)->get();

		dirExists($fileName);

		$pdf    = PDF::loadView('components.pdf_template', [
			'data'   => $data,
			'header' => [
				['NAME','text'],
				['ADDRESS','text'],
				['PHONE','text'],
				['PRODUCTION NAME','text']
			],
			'columns' => [
				'name', 'address', 'phone', 'production_name'
			],
			'modelName' => "Supplier"
		]);

        $pdf
	        ->setOptions(["isPhpEnabled"=> true, 'isRemoteEnabled'=>true])
	        ->setPaper('a4', 'potrait')
	        ->save(public_path($fileName));
	}
}