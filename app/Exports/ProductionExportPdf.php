<?php

namespace App\Exports;

use \App\Production;
use App\Components\Filters\ProductionFilter;
use Illuminate\Http\Request;
use \PDF;

class ProductionExportPdf
{
	public static function print($params = [], $fileName)
	{
		$filter = new ProductionFilter(new Request($params));
		$data   = Production::filter($filter)->get();

		dirExists($fileName);

		$pdf    = PDF::loadView('components.pdf_template', [
			'data'   => $data,
			'header' => [
				['NAME','text']
			],
			'columns' => [
				'name'
			],
			'modelName' => "Production"
		]);

        $pdf
	        ->setOptions(["isPhpEnabled"=> true, 'isRemoteEnabled'=>true])
	        ->setPaper('a4', 'potrait')
	        ->save(public_path($fileName));
	}
}